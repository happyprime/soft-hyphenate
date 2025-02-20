const path = require('path');

module.exports = {
	entry: {
		index: path.resolve(__dirname, 'src', 'index.js'),
	},
	output: {
		path: path.resolve(__dirname, 'build'),
		filename: '[name].js',
	},
	optimization: {
		minimize: true,
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env'],
						plugins: ['@babel/plugin-transform-runtime'],
					},
				},
			},
		],
	},
};
