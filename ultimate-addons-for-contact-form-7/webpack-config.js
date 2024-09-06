const path = require('path');
const glob = require('glob');
const entryPoints = {};

// const freeAppJs = glob.sync('./sass/app/js/free/*.js');
const freeAdminJs = glob.sync('./sass/admin/js/free/*.js');

//Uacf7  free 
entryPoints['ultimate-addons-for-contact-form-7/assets/admin/js/uacf7-admin-scripts.min'] = freeAdminJs;

//scss entry points 
const adminScss = glob.sync('./sass/admin/css/uacf7-admin.scss');
// 

const uacf7_config = {
    entry: entryPoints,

    output: {
        path: path.resolve(__dirname, '../'),
        filename: '[name].js',
        clean: false
    },

    /*module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    // Creates `style` nodes from JS strings
                    'style-loader',
                    // Translates CSS into CommonJS
                    'css-loader',
                    // Compiles Sass to CSS
                    'sass-loader',
                ],
            },
        ],
    }*/
}

// Export the config object.
module.exports = uacf7_config;