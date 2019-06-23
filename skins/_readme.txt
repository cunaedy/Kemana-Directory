This folder contains several folders:

I. _admin/ folder

	For ACP layout.

II. _common/ folder

	This folder hold all common skin elements, such as:
	-	Common .tpl files: commonly used tpl files, such as login form, news design, etc.
	-	CSS files for some javascripts
	-	Image files

III. _fman/ folder

	For file manager in ACP.

IV. _mail/ folder

	For email related designs.

V. _mobile/ folder

	Mobile version skins.

VI. default/ folder and perhaps other skin folders

	Common tpl files are intended to simplify you to create/modify skins. Previously you need to modify 30 tpl files, you are now only need to modify
	files: layout.css, outline.tpl, welcome.tpl, section.tpl & popup.tpl. You can use common template for the rest of layout, eg
	page.tpl, account.tpl, etc. But if you need to create a custom layout for those .tpl, you can include the files in your skin folder, eg. you need
	to customize news page, simply create news.tpl and put in your skin folder, and qE will then use your file.