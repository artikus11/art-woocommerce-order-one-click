const path                 = require( 'path' );
const glob                 = require( 'glob' );
const defaultConfig        = require( "@wordpress/scripts/config/webpack.config" );
const BrowserSyncPlugin    = require( 'browser-sync-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const CssMinimizerPlugin   = require( "css-minimizer-webpack-plugin" );
const CopyPlugin           = require( 'copy-webpack-plugin' );
const SpriteLoaderPlugin   = require( 'svg-sprite-loader/plugin' );
const { hasBabelConfig }   = require( '@wordpress/scripts/utils' );
const TerserPlugin         = require( 'terser-webpack-plugin' );

const isProduction = process.env.NODE_ENV === 'production';
const mode         = isProduction ? 'production' : 'development';

const BUILD_DIR = path.resolve( __dirname, 'assets' );

const filename = ext => isProduction ? ext + '/[name].min.' + ext : ext + '/[name].' + ext;

module.exports = {
	...defaultConfig,
	mode,
	devtool:      ! isProduction ? 'source-map' : false,
	entry:        {
		"awooc-scripts":                 path.resolve( process.cwd(), 'src/js', 'awooc-scripts.js' ),
		"admin-script":                 path.resolve( process.cwd(), 'src/js', 'admin-script.js' ),
		"awooc-styles":                 path.resolve( process.cwd(), 'src/scss', 'awooc-styles.scss' ),
		"admin-style":                 path.resolve( process.cwd(), 'src/scss', 'admin-style.scss' ),
	},
	output:       {
		filename: filename( 'js' ),
		path:     BUILD_DIR,
		clean:    true
	},
	optimization: {
		minimize:  isProduction,
		minimizer: [
			new CssMinimizerPlugin( {
				minimizerOptions: {
					preset: [
						"default",
						{ "discardComments": { "removeAll": true } }
					]
				},
			} ),
			new TerserPlugin(),
		]
	},
	module:       {
		rules: [
			{
				test:    /\.js$/,
				exclude: /node_modules/,
				use:     [
					require.resolve( 'thread-loader' ),
					{
						loader:  require.resolve( 'babel-loader' ),
						options: {
							// Babel uses a directory within local node_modules
							// by default. Use the environment variable option
							// to enable more persistent caching.
							cacheDirectory: process.env.BABEL_CACHE_DIRECTORY || true,

							// Provide a fallback configuration if there's not
							// one explicitly available in the project.
							...(
								! hasBabelConfig() && {
									babelrc:    false,
									configFile: false,
									presets:    [ require.resolve( '@wordpress/babel-preset-default' ) ],
								}
							),
						},
					},
				],
			},
			{
				test:      /\.(?:ico|gif|png|jpg|jpeg|svg)$/i,
				include:   path.resolve( __dirname, 'src/images' ),
				exclude: path.resolve( __dirname, 'src/icons' ),
				type:      'asset/resource',
				generator: {
					filename: "images/[name][ext]",
				},
			},
			{
				test:      /\.(woff|woff2|eot|ttf|otf|svg)$/i,
				include:   path.resolve( __dirname, 'src/fonts' ),
				type:      'asset/resource',
				generator: {
					filename: "fonts/[name][ext]",
				},
			},
			{
				test:    /\.svg$/,
				include: path.resolve( __dirname, 'src/icons' ),
				use:     [
					{
						loader:  'svg-sprite-loader',
						options: {
							symbolId:       filePath => {
								return 'icon-' + path
									.basename( filePath )
									.replace( ".svg", "" )
									.toLowerCase();
							},
							extract:        true,
							spriteFilename: 'images/icons.svg',
							//publicPath: 'images'
						}
					},
					'svgo-loader'
				]
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

		new MiniCssExtractPlugin( {
			filename: filename( 'css' ),
		} ),
		new SpriteLoaderPlugin( {
			plainSprite: true,
		} )
	],

	externals: {
		jquery: 'jQuery'
	}
}
