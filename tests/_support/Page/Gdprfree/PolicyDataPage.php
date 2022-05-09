<?php
/**
 * Selectors used in the automation testcases for policy data of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used for policy data of GDPR cookie consent plugin
 * 
 * @category  AutomationTests
 * @package   WordPress_WPlegalpages_Pro_Plugin
 * @author    WPEKA <hello@wpeka.com>
 * @copyright 2022 WPEKA
 * @license   GPL v3
 * @link      https://club.wpeka.com
 * 
 * @since 1.0
 */
class PolicyDataPage
{
    public $gdprCookieConset = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
    public $policyData = '#toplevel_page_gdpr-cookie-consent > ul > li:nth-child(4) > a';
    public $addNew = '#wpbody-content > div.wrap > a.page-title-action';
    public $addTitle = '#title';
    public $addTitleValue = 'Policy1';
    public $purpose = '#tinymce';
    public $purposeValue = 'This is cookie policy1';
    public $iframe = '#content_ifr';
    public $domain = '#_gdpr_policies_domain > div.inside > #_gdpr_policies_domain';
    public $domainValue = 'https://club.wpeka.com';
    public $addMedia = '#wp-_gdpr_policies_links_editor-media-buttons > button';
    public $insertFromURL = '#menu-item-embed';
    public $urlField = '#embed-url-field';
    public $urlValue = 'http://google.com';
    public $insertInToPost = '#__wp-uploader-id-0 > div.media-frame-toolbar > div > div.media-toolbar-primary.search-form > button';
    public $publishlabel = '#submitdiv > div.postbox-header > h2';
    public $publish = '#publish';
    public $pageMenu = '#menu-pages > a > div.wp-menu-name';
    public $allPages = '#menu-pages > ul > li.wp-first-item > a';
    public $addNewPage = '#wpbody-content > div.wrap > a';
    public $addPageTitle = '#editor > div > div.edit-post-layout.is-mode-visual.is-sidebar-opened.interface-interface-skeleton.has-footer > div.interface-interface-skeleton__editor > div.interface-interface-skeleton__body > div.interface-interface-skeleton__content > div.edit-post-visual-editor > div.edit-post-visual-editor__content-area > div > div.editor-styles-wrapper.block-editor-writing-flow > div.edit-post-visual-editor__post-title-wrapper > h1';
    public $addPageTitleValue = 'Policy';
    public $addicon = '#editor > div > div.edit-post-layout.is-mode-visual.is-sidebar-opened.interface-interface-skeleton.has-footer > div.interface-interface-skeleton__editor > div.interface-interface-skeleton__body > div.interface-interface-skeleton__content > div.edit-post-visual-editor > div.edit-post-visual-editor__content-area > div > div.editor-styles-wrapper.block-editor-writing-flow > div.is-root-container.block-editor-block-list__layout > div > div > div > button > svg';
    public $search = '#editor > div > div.popover-slot > div > div > div > div > div.components-base-control.block-editor-inserter__search.components-search-control.css-1wzzj1a.e1puf3u3 > div > div > input';
    public $searchValue = 'GDPR';
    public $gdprCookieDetail = ' div.block-editor-block-types-list__list-item > button > span.block-editor-block-types-list__item-title';
    public $publish1 = '#editor > div > div.edit-post-layout.is-mode-visual.is-sidebar-opened.interface-interface-skeleton.has-footer > div.interface-interface-skeleton__editor > div.interface-interface-skeleton__header > div > div.edit-post-header__settings > button.components-button.editor-post-publish-panel__toggle.editor-post-publish-button__button.is-primary';
    public $publish2 = '#editor > div > div.edit-post-layout.is-mode-visual.is-sidebar-opened.interface-interface-skeleton.has-footer > div.interface-interface-skeleton__editor > div.interface-interface-skeleton__body > div.interface-interface-skeleton__actions > div:nth-child(2) > div > div > div.editor-post-publish-panel__header > div.editor-post-publish-panel__header-publish-button > button';
    public $viewPage = '#editor > div > div.edit-post-layout.is-mode-visual.is-sidebar-opened.interface-interface-skeleton.has-footer > div.interface-interface-skeleton__editor > div.interface-interface-skeleton__body > div.interface-interface-skeleton__notices > div > div > div > div > div > a';

    public $policy1='Policy1';
    public $editedAddTitleValue = 'Policy2';
    public $editedPurposeValue  = 'This is cookie policy2';
    public $update = '#publish';
    public $gdpr = '#wp-admin-bar-site-name > a';

    public $policy2 = '#the-list > tr:nth-child(1) > td:nth-child(2) > strong';
    public $quick_Edit = '#the-list > tr > td.title.column-title.has-row-actions.column-primary.page-title > div.row-actions > span.inline.hide-if-no-js > button';
    public $policyTitle = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > label > span.input-text-wrap > input';
    public $policyTitleValue = 'Policy3';
    public $month = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > fieldset > div > label:nth-child(1) > select';
    public $monthValue = '03-Mar';
    public $date = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > fieldset > div > label:nth-child(2) > input';
    public $dateValue = '06';
    public $year = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > fieldset > div > label:nth-child(3) > input';
    public $yearValue = '2022';
    public $timeInHour = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > fieldset > div > label:nth-child(4) > input';
    public $timeInHourValue = '11';
    public $timeInMinute = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > fieldset.inline-edit-col-left > div > fieldset > div > label:nth-child(5) > input';
    public $timeInMinuteValue = '20'; 
    public $updateButton = '#the-list > tr.inline-edit-row.inline-edit-row-page.quick-edit-row.quick-edit-row-page.inline-edit-gdprpolicies.inline-editor > td > div > button.button.button-primary.save.alignright';

    public $policy3 = 'Policy3';
    public $moveToTrash = '#delete-action > a';
}
