const fs = require('fs');
const path = require('path');

/**
 * Function to check if the source is a folder.
 * @param  {String}  source Path to folder/file.
 * @return {Boolean}        If the current item is a directoy.
 */
const isDirectory = source => fs.lstatSync(source).isDirectory();

/**
 * Function to check if the source is a file.
 * @param  {String}  source Path to folder/file.
 * @return {Boolean}        If the current item is a file.
 */
const isFile = source => fs.lstatSync(source).isFile();

/**
 * Function that returns an array of all directories within the provided source directory.
 * @param  {String} source 	Path to folder to retrieve sub-folders from.
 * @return {Array}        	Array of sub-folder paths.
 */
const getDirectories = source => fs.readdirSync(source).map(name => path.join(source, name)).filter(isDirectory);

/**
 * Function that returns an array of all files within the provided source directory.
 * @param  {String} source 	Path to folder to retrieve files from.
 * @return {Array}        	Array of file paths.
 */
const getFiles = source => fs.readdirSync(source).map(name => path.join(source, name)).filter(isFile);

exports.isDirectory = isDirectory;
exports.getDirectories = getDirectories;
exports.isFile = isFile;
exports.getFiles = getFiles;
