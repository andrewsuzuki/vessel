# Vessel Notes

## Controllers with notes

### FrontController

	Handles visitors accessing non-administration pages and files.

	**getPage** - gets page: examines uri, determines requesting page, checks db for existence, gets page content from file, inserts page content into theme, makes view with meta (from db)

### BackController

	Gets vessel admin

	(previously HomeController)

### ApiController

	Handles json ajax requests from ember (?) on client side of vessel admin.

## Page Setup

Record of page saved in db
History of edits (with fields and raw content) saved in relation to page record
Current content stored raw (as user typed it) in storage/content/pages
Current content stored compiled (if markdown: markdown -> html) in storage/content/pages/compiled

request:
	1. get record from db
	2. 	a) if it's a compiled format (like md), check storage/content/pages/compiled
			if not there, then check ../, then compile and use
		b) if it's not a comp. format, check storage/content/pages and use
	
	If not found in db, or not in storage/content/pages/compiled or ../, return 404.

saving:
	1. if editing and fields or content changed:
		insert new history row with relation to page, with old fields, updated_at timestamp AS created_at, user_id, and raw content
			if raw content isn't changing, set as NULL
	2. save raw in storage/content/pages
	3. compile if it's a compiled format (like md)

## Routes with notes

pages
{
	table with select box, title (linked to page), parent, edit/delete button
}

	new page
	{
		slug
		title
		description
		content
		parent page
		sub-template
		
		preview (popup)
		save
		save draft
		save as (duplicator)
		delete

		edit history table, with title, "compare" popup with diffs from previous version of choice, user, date, force delete
	}

	edit page
	{
		""""""
	}

blocks

## API

/api
{
	GET /pages
	POST /pages/create
	PUT /pages/edit

	GET /blocks
	POST /blocks/create
	PUT /blocks/edit
	
	GET /media
	GET /media/image
	POST /media/image
	GET /media/file
	POST /media/file

	GET /settings
	POST /settings
}

## Permissions

Create page
Edit page (++ whitelist/or/blacklist for specific pages)
Delete page
Create block
Edit block
Delete block
Manage users and permissions
Edit site settings