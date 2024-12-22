module.exports = {
    extends: [
      'plugin:@wordpress/eslint-plugin/recommended'
    ],
    env: {
      browser: true,
      es2021: true,
      jquery: true
    },
    parserOptions: {
      ecmaVersion: 12,
      sourceType: 'module'
    },
    rules: {
      'no-console': ['error', { allow: ['warn', 'error'] }],
      'import/no-unresolved': 'off'
    }
  };