const path                    = require( 'path' );
const defaultConfig           = require( "@wordpress/scripts/config/webpack.config" );
const MiniCssExtractPlugin    = require( 'mini-css-extract-plugin' );
const CssMinimizerPlugin      = require( "css-minimizer-webpack-plugin" );
const { hasBabelConfig }      = require( '@wordpress/scripts/utils' );
const TerserPlugin            = require( 'terser-webpack-plugin' );
const UnminifiedWebpackPlugin = require( 'unminified-webpack-plugin' );
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const isProduction = process.env.NODE_ENV === 'production';
const mode         = isProduction ? 'production' : 'development';

const BUILD_DIR = path.resolve( __dirname, 'assets' );

const filename = ext => isProduction ? ext + '/[name].min.' + ext : ext + '/[name].min.' + ext;

module.exports = {
	...defaultConfig,
	mode,
	//devtool:      ! isProduction ? 'source-map' : false,
	devtool:      ! isProduction ? false : false,
	entry:        {
		"awooc-scripts": path.resolve( process.cwd(), 'src/js', 'awooc-scripts.js' ),
		"awooc-public-script": path.resolve( process.cwd(), 'src/js/public', 'main.js' ),
		"awooc-admin-script":  path.resolve( process.cwd(), 'src/js/admin', 'script.js' ),
		"awooc-styles":  path.resolve( process.cwd(), 'src/scss', 'awooc-styles.scss' ),
		"admin-style":   path.resolve( process.cwd(), 'src/scss', 'admin-style.scss' ),
	},
	output:       {
		filename: filename( 'js' ),
		path:     BUILD_DIR,
		clean:    true
	},
	optimization: {
		minimize:  true,
		minimizer: [
			new CssMinimizerPlugin( {
				minimizerOptions: {
					preset: [
						"default",
						{ "discardComments": { "removeAll": true } }
					]
				},
			} ),
			new TerserPlugin( {
				extractComments: false,
			} ),
		]
	},
	module:       {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [
					require.resolve('babel-loader'),
					{
						loader: require.resolve('babel-loader'),
						options: {
							cacheDirectory: process.env.BABEL_CACHE_DIRECTORY || true,
							presets: [
								require.resolve('@wordpress/babel-preset-default'),
								require.resolve('@babel/preset-env'),
							],
						},
					},
				],
			},
			{
				test: /\.css$/i,
				use:  [ "style-loader", "css-loader" ],
			},
			{
				test:    /\.s[ac]ss$/i,
				exclude: /node_modules/,
				use:     [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					{
						loader:  'css-loader',
						options: {
							sourceMap: ! isProduction,
						},
					},
					{
						loader:  'postcss-loader',
						options: {
							sourceMap: ! isProduction,
						},
					},
					{
						loader:  'sass-loader',
						options: {
							sourceMap: ! isProduction,
						},
					},
				],
			},
		],
	},
	plugins:      [
		new RemoveEmptyScriptsPlugin(),
		new MiniCssExtractPlugin( {
			filename: filename( 'css' ),
		} ),
		new UnminifiedWebpackPlugin( )
	],

	externals: {
		jquery: 'jQuery'
	}
}
