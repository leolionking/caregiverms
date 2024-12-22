const { merge } = require('webpack-merge');
const baseConfig = require('./base.config.js');

module.exports = merge(baseConfig, {
  mode: 'development',
  devtool: 'source-map',
  stats: {
    colors: true,
    modules: true,
    reasons: true,
    errorDetails: true
  },
  watchOptions: {
    ignored: /node_modules/,
    aggregateTimeout: 300,
    poll: 1000
  }
});