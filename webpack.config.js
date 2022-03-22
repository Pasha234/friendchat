const path = require('path')
const webpack = require('webpack')

module.exports = {
  resolve: {
    extensions: ['.js', '.vue'],
    alias: {
      '@': path.resolve(__dirname, './resources')
    }
  },
}