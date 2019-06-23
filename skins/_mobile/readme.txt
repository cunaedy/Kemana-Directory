Since qEngine 6.0 (and its derivates, such as Cart Engine 3.0), it no longer needs a seperate skin for mobile version,
instead it uses "responsive design" to adapt to different screen size on various devices (desktops, laptops, tablets,
smartphones, etc).

But some 3rd party skin may not be 'responsive', or you may need a really different design for mobile devices,
qEngine also accomodates that. To enable mobile skin please do the following:

1. Go to ACP > Tools > Site Configuration > Engine Settings.
2. Open the tab "Looks & Feels", scroll to "Enable Mobile Version Skin?", select "Yes".
3. Now, you need to create the skin for mobile version:
	- First, you need to supply the same skin files as desktop version files. Simply copy & paste the *.tpl files from
	  /skins/_common & /skins/default folders to /skins/_mobile folder.
	- Then, edit the .tpl files to match your mobile design
	- If you didn't copy several .tpl files for mobile design, qEngine will automatically search in /skins/_common
	  folder, if it still can't find the file, it will throw an error screen.
	- That's all!
	
To switch from desktop skin to mobile skin use this url:
<a href="index.php?cmd=viewmode&amp;mode=mobile">Mobile View</a>

And to switch from mobile skin to desktop skin:
<a href="index.php?cmd=viewmode&amp;mode=desktop">Desktop View</a>

Good luck!