#!/usr/bin/env bash
# PHPCS (WordPress + WordPress-VIP-Go) check for merge requests.
#
# - Runs phpcs on the PHP files changed by the MR.
# - Keeps only violations on lines the MR adds or modifies ("new code").
# - Posts one inline MR thread per offending line, plus a single summary note
#   that is updated in place on every pipeline run.
# - Exits non-zero when any ERROR is found in new code (warnings are reported
#   but do not fail the job).
#
# Required env: PHPCS_BIN, plus the predefined GitLab MR pipeline variables.
# Optional env: GITLAB_TOKEN (api scope) to post comments,
#               PHPCS_STANDARD (default phpcs.xml.dist),
#               PHPCS_MAX_INLINE_COMMENTS (default 50).

set -euo pipefail

PHPCS_BIN="${PHPCS_BIN:?PHPCS_BIN must point to the phpcs executable}"
MAX_INLINE="${PHPCS_MAX_INLINE_COMMENTS:-50}"
MR_API="$CI_API_V4_URL/projects/$CI_PROJECT_ID/merge_requests/$CI_MERGE_REQUEST_IID"
SUMMARY_MARKER='<!-- phpcs-bot:summary -->'
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

api() {
	curl --silent --show-error --fail --header "PRIVATE-TOKEN: $GITLAB_TOKEN" "$@"
}

can_comment() {
	[ -n "${GITLAB_TOKEN:-}" ]
}

# Create or update the single summary note on the MR.
post_summary() {
	local body="$1" note_id page=1 notes
	can_comment || return 0
	note_id=""
	while :; do
		notes=$(api "$MR_API/notes?per_page=100&page=$page") || return 0
		[ "$(jq length <<<"$notes")" -eq 0 ] && break
		note_id=$(jq -r --arg m "$SUMMARY_MARKER" '[.[] | select(.body | contains($m))][0].id // empty' <<<"$notes")
		[ -n "$note_id" ] && break
		page=$((page + 1))
	done
	if [ -n "$note_id" ]; then
		api --request PUT --header "Content-Type: application/json" \
			--data "$(jq -n --arg body "$body" '{body: $body}')" \
			"$MR_API/notes/$note_id" >/dev/null || echo "WARN: could not update summary note"
	else
		api --request POST --header "Content-Type: application/json" \
			--data "$(jq -n --arg body "$body" '{body: $body}')" \
			"$MR_API/notes" >/dev/null || echo "WARN: could not post summary note"
	fi
}

if ! can_comment; then
	echo "WARN: GITLAB_TOKEN is not set - results will only appear in this job log."
fi

# --- Work out what "new code" is -------------------------------------------

BASE_SHA="${CI_MERGE_REQUEST_DIFF_BASE_SHA:?not a merge request pipeline}"
HEAD_SHA="$CI_COMMIT_SHA"
START_SHA=""
POSITION_HEAD_SHA=""

if can_comment; then
	MR_JSON=$(api "$MR_API")
	BASE_SHA=$(jq -r '.diff_refs.base_sha // empty' <<<"$MR_JSON")
	START_SHA=$(jq -r '.diff_refs.start_sha // empty' <<<"$MR_JSON")
	POSITION_HEAD_SHA=$(jq -r '.diff_refs.head_sha // empty' <<<"$MR_JSON")
	BASE_SHA="${BASE_SHA:-$CI_MERGE_REQUEST_DIFF_BASE_SHA}"
fi

git cat-file -e "$BASE_SHA^{commit}" 2>/dev/null ||
	git fetch --quiet origin "$CI_MERGE_REQUEST_TARGET_BRANCH_NAME"

mapfile -t FILES < <(
	git diff --name-only --diff-filter=ACMR "$BASE_SHA" "$HEAD_SHA" -- '*.php' |
		grep -vE '(^|/)(vendor|node_modules)/|^\.gitlab/' || true
)

if [ "${#FILES[@]}" -eq 0 ]; then
	echo "No PHP files changed in this merge request - nothing to check."
	post_summary "$SUMMARY_MARKER
### PHPCS / WordPress VIP

:white_check_mark: No PHP files changed in this merge request."
	exit 0
fi

echo "Checking ${#FILES[@]} changed PHP file(s):"
printf '  %s\n' "${FILES[@]}"

# "path:line" for every line added or modified by the MR.
git diff -U0 --no-color "$BASE_SHA" "$HEAD_SHA" -- "${FILES[@]}" | awk '
	/^\+\+\+ / { file = substr($0, 7); next }
	/^@@ / {
		split(substr($3, 2), range, ",")
		count = (range[2] == "" ? 1 : range[2])
		for (i = 0; i < count; i++) print file ":" (range[1] + i)
	}
' >"$WORK/added.txt"

# --- Run phpcs -------------------------------------------------------------

# phpcs exits non-zero whenever it finds anything; we judge the JSON instead.
"$PHPCS_BIN" --standard="${PHPCS_STANDARD:-phpcs.xml.dist}" --report=json --report-file="$WORK/phpcs.json" \
	-q "${FILES[@]}" || true

if ! jq -e '.files' "$WORK/phpcs.json" >/dev/null 2>&1; then
	echo "ERROR: phpcs did not produce a valid report."
	cat "$WORK/phpcs.json" 2>/dev/null || true
	exit 1
fi

jq --arg root "$CI_PROJECT_DIR/" --rawfile added "$WORK/added.txt" '
	($added | split("\n") | map(select(length > 0) | {key: ., value: true}) | from_entries) as $new
	| [ .files | to_entries[]
		| (.key | ltrimstr($root)) as $path
		| .value.messages[]
		| select($new["\($path):\(.line)"])
		| {path: $path, line, column, type, message, source}
	  ]
	| sort_by(.type != "ERROR", .path, .line)
' "$WORK/phpcs.json" >"$WORK/new.json"

ERRORS=$(jq '[.[] | select(.type == "ERROR")] | length' "$WORK/new.json")
WARNINGS=$(jq '[.[] | select(.type == "WARNING")] | length' "$WORK/new.json")
EXISTING=$(jq --argjson n "$(jq length "$WORK/new.json")" \
	'([.files[] | .errors + .warnings] | add // 0) - $n' "$WORK/phpcs.json")

echo
echo "New code: $ERRORS error(s), $WARNINGS warning(s). Pre-existing issues in touched files: $EXISTING."
jq -r '.[] | "  \(.type)  \(.path):\(.line):\(.column)  \(.message)  [\(.source)]"' "$WORK/new.json"

# --- Inline comments -------------------------------------------------------

INLINE_POSTED=0
if can_comment && [ -n "$POSITION_HEAD_SHA" ]; then
	if [ "$POSITION_HEAD_SHA" != "$HEAD_SHA" ]; then
		echo "MR head ($POSITION_HEAD_SHA) differs from this pipeline's commit - skipping inline comments."
	else
		# Bodies of existing notes, so re-runs do not duplicate threads.
		page=1
		: >"$WORK/existing_bodies.json"
		while :; do
			discussions=$(api "$MR_API/discussions?per_page=100&page=$page") || break
			[ "$(jq length <<<"$discussions")" -eq 0 ] && break
			jq '[.[].notes[].body]' <<<"$discussions" >>"$WORK/existing_bodies.json"
			page=$((page + 1))
		done
		jq -s 'add // []' "$WORK/existing_bodies.json" >"$WORK/existing.json"

		while IFS= read -r group; do
			[ "$INLINE_POSTED" -ge "$MAX_INLINE" ] && break
			path=$(jq -r '.path' <<<"$group")
			line=$(jq -r '.line' <<<"$group")
			body=$(jq -r '
				"**PHPCS / WordPress VIP**\n\n"
				+ ([.items[] | "- \(if .type == "ERROR" then ":red_circle: **Error**" else ":warning: **Warning**" end) `\(.source)`: \(.message)"] | join("\n"))
				+ "\n\n<!-- phpcs-bot:\(.path):\(.line) -->"
			' <<<"$group")

			if jq -e --arg b "$body" 'index($b) != null' "$WORK/existing.json" >/dev/null; then
				continue
			fi

			payload=$(jq -n --arg body "$body" --arg path "$path" --argjson line "$line" \
				--arg base "$BASE_SHA" --arg start "$START_SHA" --arg head "$POSITION_HEAD_SHA" '{
					body: $body,
					position: {
						position_type: "text",
						base_sha: $base, start_sha: $start, head_sha: $head,
						old_path: $path, new_path: $path, new_line: $line
					}
				}')
			if api --request POST --header "Content-Type: application/json" --data "$payload" \
				"$MR_API/discussions" >/dev/null; then
				INLINE_POSTED=$((INLINE_POSTED + 1))
			else
				echo "WARN: could not post inline comment on $path:$line"
			fi
		done < <(jq -c 'group_by([.path, .line])[] | {path: .[0].path, line: .[0].line, items: .}' "$WORK/new.json")
	fi
fi

# --- Summary note ----------------------------------------------------------

if [ "$ERRORS" -gt 0 ]; then
	STATUS=":x: **$ERRORS error(s)** and **$WARNINGS warning(s)** in new code. Errors block this merge request - please fix them and push again."
elif [ "$WARNINGS" -gt 0 ]; then
	STATUS=":warning: No errors, **$WARNINGS warning(s)** in new code. Warnings do not block the merge, but please review them."
else
	STATUS=":white_check_mark: No PHPCS / WordPress VIP issues in new code."
fi

TABLE=$(jq -r '
	if length == 0 then "" else
		"| | File | Rule | Message |\n|---|---|---|---|\n"
		+ ([.[:200][]
			| "| \(if .type == "ERROR" then ":red_circle:" else ":warning:" end) "
			+ "| `\(.path):\(.line)` | `\(.source)` | \(.message | gsub("\\|"; "\\|") | gsub("\n"; " ")) |"
		  ] | join("\n"))
		+ (if length > 200 then "\n\n_…and \(length - 200) more - see the job log._" else "" end)
	end
' "$WORK/new.json")

post_summary "$SUMMARY_MARKER
### PHPCS / WordPress VIP

$STATUS

$TABLE

<sub>Standards: \`WordPress\` + \`WordPress-VIP-Go\` · Commit \`${HEAD_SHA:0:8}\` · $EXISTING pre-existing issue(s) in touched files are not counted · [Job log]($CI_JOB_URL)</sub>"

[ "$ERRORS" -eq 0 ]
