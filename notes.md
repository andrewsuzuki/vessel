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