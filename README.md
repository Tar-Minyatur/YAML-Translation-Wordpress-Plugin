# YAML Translation Wordpress Plugin

The goal of this plugin is to support the bigger translation efforts of international projects.
In order to keep the effort for volunteers and the project members low, it is often easiest to decouple translation
as much as possible from the rest of the work.

This plugin allows translations to be maintained in well-structured YAML files and allows the injection of the
text blocks into WordPress pages and posts.

## Project Status

**This project is in its infancy.** The idea came together on two subsequent nights in the chat of the Folding@home
developer community and what you see here should be considered nothing more than a proof of concept. :)

## Requirements

* PHP 7.4+
* PHP YAML extension
* Wordpress 5.4+

## Usage

1. Clone/download the repository
2. Drop the entire folder `yaml-translation/` into the `wp-content/plugins/` folder of your Wordpress installation
3. Open the admin panel and look for the plugin; install it
4. Create some YAML files with pre-defined text blocks (see below for the required structure)
5. Add some posts/pages to your blog and use the two shortcodes provided by this plugin:
   * `[set_translation file="home"]` once per post to define which file to read from
   * `[textblock id=2]` to insert text somewhere
   * Alternatively you can also use `[textblock]2[/textblock]` if you prefer that
   
Wordpress automatically figures out the user's language and will try to render the text in the right language.
At the moment **en_US is hardcoded as default language** and the plugin will always fall back on that.

## Translations Directory/File Structure

For now, the plugin makes the following assumptions:
* All translations are located in `wp-content/Localization/`
* In there, everything is organized by locales, i.e. there are subfolders for each locale (e.g. `en_US/`)
* Each of those folders can contain any number of `.yaml` files
* Each YAML file should have a root node `text` with an array of text blocks

Here is a sample folder structure:

    wp-content/
       Localization/
          en_US/
             home.yaml
             about-us.yaml
          de_DE/
             home.yaml
             about-us.yaml
             
And one of the files might look like this:

    ---
    title: "Home"
    text: [
    "This is the first block. It will have ID 0.",
    "And the second block will go by ID 2."
    ]

## Extras

There are currently a few additional "features" that I'm still undecided on, but they work for now:
* If there is a `title` property (as shown above), then this will be available with the ID 'title'
* It should in theory be possible to use key instead of the array and the keys would work as IDs, but it's not tested:

       text:
          welcome_message: "Welcome to our website!"
          shoutout: "Thanks for the awesome YAML translation plugin goes to @Tar-Minyatur"

* The block shortcode can also request a block from a different file: `[textblock file="about-us" id=2]`
* There is currently an overview of all available and translated text blocks in the administration panel. It's located
  under `Settings > YAML Translation`

## Troubleshooting

Look into the error log of your webserver. The plugin current logs messages there.

If something doesn't work it most likely has to do with a wrong directory or file structure. At the moment the plugin is
not really flexible. 

## Kudos

Big cheers to the following OpenSource projects that made this one a breeze to set up:

* [Parsedown](https://github.com/erusev/parsedown) (Markdown parser, MIT license)