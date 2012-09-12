SEOMetaFields
=============

This is a super simple WordPress plugin that adds some basic meta-tag functionality.

##Background
The "SEO Dude" for one of my clients wanted a simple way to add default meta tags to pages \
while still having some control over the tags at a basic level.  So I wrote a plugin to do exactly that.

##What it Does

This plugin is simple.  It adds:

+ an admin page where you can set site defaults for keyword, title, and description tags

+ a pane on the "add post" where you can create metadata for all types of content that you might add from the admin (posts, pages, and custom0 -- and it figures out what content types there are and adds it for each one.  And, of course, if this is not set it goes to the defaults set in the admin page.

+ a function to insert this into your theme.

##Usage

You'll have to checkout this repo and install it... it's all of one file, so no biggy.

After you've activated it and set your defaults, you'll want to add the function to the theme.

The function is strightforward... just pass it a string and it outputs the tag.

get_SimpleSEO($fieldType);

This takes the field type as a string [ "title" | "description" | "keyword"] and should be placed in an appropriate spot in the theme's headers.

##Cavet Emptor
I really just wanted a plugin that I didn't have to read carefully and search for malware, that did only 
this one thing effectively, and was not an issue to use.

I don't have time to support this, but it migh tbe useful, so here it is in all it's non validating glory.