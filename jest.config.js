module.exports = {
	testPathIgnorePatterns: ['/node_modules/'],
	preset: '@vue/cli-plugin-unit-jest',
	testEnvironment: 'jsdom',
	testMatch:["**/src/JSTests/*.test.js"],
  }