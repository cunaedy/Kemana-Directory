-- 1. Recreate tables that may be altered
DROP TABLE IF EXISTS `__PREFIX__listing_cf_value`;
CREATE TABLE `__PREFIX__listing_cf_value` (
  `idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `cf_1` varchar(255) NOT NULL,
  `cf_2` text NOT NULL,
  `cf_3` varchar(255) NOT NULL,
  `cf_4` varchar(255) NOT NULL,
  `cf_5` varchar(255) NOT NULL,
  `cf_6` varchar(255) NOT NULL,
  `cf_7` varchar(255) NOT NULL,
  `cf_8` varchar(255) NOT NULL,
  `cf_9` varchar(255) NOT NULL,
  `cf_10` varchar(255) NOT NULL,
  `cf_11` varchar(255) NOT NULL,
  `cf_12` varchar(255) NOT NULL,
  PRIMARY KEY (`idx`),
  KEY `item_id` (`item_id`),
  KEY `cf_1` (`cf_1`),
  KEY `cf_3` (`cf_3`),
  KEY `cf_4` (`cf_4`),
  KEY `cf_5` (`cf_5`),
  KEY `cf_6` (`cf_6`),
  KEY `cf_7` (`cf_7`),
  KEY `cf_8` (`cf_8`),
  KEY `cf_9` (`cf_9`),
  KEY `cf_10` (`cf_10`),
  KEY `cf_11` (`cf_11`),
  KEY `cf_12` (`cf_12`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 2. Clean all tables

TRUNCATE TABLE `__PREFIX__cache`;
TRUNCATE TABLE `__PREFIX__ip_log`;
TRUNCATE TABLE `__PREFIX__listing`;
TRUNCATE TABLE `__PREFIX__listing_cat`;
TRUNCATE TABLE `__PREFIX__listing_cf_define`;
TRUNCATE TABLE `__PREFIX__listing_dir`;
TRUNCATE TABLE `__PREFIX__mailog`;
TRUNCATE TABLE `__PREFIX__menu_item`;
TRUNCATE TABLE `__PREFIX__menu_set`;
TRUNCATE TABLE `__PREFIX__module`;
TRUNCATE TABLE `__PREFIX__module_pos`;
TRUNCATE TABLE `__PREFIX__notification`;
TRUNCATE TABLE `__PREFIX__order`;
TRUNCATE TABLE `__PREFIX__page`;
TRUNCATE TABLE `__PREFIX__page_cat`;
TRUNCATE TABLE `__PREFIX__page_group`;
TRUNCATE TABLE `__PREFIX__permalink`;
TRUNCATE TABLE `__PREFIX__qadmin_log`;
TRUNCATE TABLE `__PREFIX__qcomment`;
TRUNCATE TABLE `__PREFIX__qcomment_set`;
TRUNCATE TABLE `__PREFIX__user`;

-- 3. Refill tables

INSERT INTO `__PREFIX__listing` (`idx`, `dir_id`, `owner_id`, `owner_email`, `owner_passwd`, `category_1`, `category_2`, `category_3`, `category_4`, `category_5`, `category_6`, `item_permalink`, `item_title`, `item_url`, `item_url_mask`, `item_summary`, `item_details`, `item_status`, `item_sort_point`, `item_class`, `item_date`, `item_update`, `item_valid_date`, `item_backlink_url`, `item_backlink_ok`, `item_visibility`, `item_keyword`, `item_rating`, `item_votes`, `see_also`, `stat_hits`, `menu_idx`, `original_idx`, `expired_email`, `orphaned`, `last_calculated`, `smart_search`) VALUES
(1, 1,  'admin',  'contact@c97.net',  '', 1,  0,  0,  0,  0,  0,  'example-listing.php',  'Example Listing',  '', '', '', 'This is just a sample listing. You can remove it from ACP.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. In non urna erat. Aliquam placerat imperdiet sem sit amet consequat. Sed convallis augue vitae nisi efficitur ullamcorper. Etiam eu imperdiet magna. Proin quis aliquam elit. Aenean facilisis viverra accumsan. Donec porttitor euismod nibh at dapibus.', 'P',  0,  'R',  '2016-09-25', '0000-00-00', '2016-09-25', '', '0',  'A',  '', 0.00, 0,  '', 3,  0,  0,  '0',  '0',  '2017-09-03', 'example listing this is just a sample you can remove it from acp lorem ipsum dolor sit amet consectetur adipiscing elit in non urna erat aliquam placerat imperdiet sem consequat sed convallis augue vitae nisi efficitur ullamcorper etiam eu magna proin quis aenean facilisis viverra accumsan donec porttitor euismod nibh at dapibus '),
(2, 1,  'admin',  'admin@admin.com',  '', 1,  0,  0,  0,  0,  0,  'microsoft.php',  'Microsoft',  'https://www.microsoft.com',  '', '', 'Microsoft Corporation (commonly referred to as Microsoft or MS) is an American multinational technology company headquartered in Redmond, Washington, that develops, manufactures, licenses, supports and sells computer software, consumer electronics and personal computers and services. Its best known software products are the Microsoft Windows line of operating systems, Microsoft Office office suite, and Internet Explorer and Edge web browsers. Its flagship hardware products are the Xbox video game consoles and the Microsoft Surface tablet lineup. As of 2011, it was the world&#039;s largest software maker by revenue, and one of the world&#039;s most valuable companies.',  'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 27, 0,  0,  '0',  '0',  '2017-09-03', 'microsoft corporation commonly referred to as or ms is an american multinational technology company headquartered in redmond washington that develops manufactures licenses supports and sells computer software consumer electronics personal computers services its best known products are the windows line of operating systems office suite internet explorer edge web browsers flagship hardware xbox video game consoles surface tablet lineup 2011 it was worlds largest maker by revenue one most valuable companies way us '),
(3, 1,  'admin',  'admin@admin.com',  '', 1,  0,  0,  0,  0,  0,  'apple.php',  'Apple',  'http://www.apple.com/',  '', '', 'Apple Inc. is an American multinational technology company headquartered in Cupertino, California, that designs, develops, and sells consumer electronics, computer software, and online services. Its hardware products include the iPhone smartphone, the iPad tablet computer, the Mac personal computer, the iPod portable media player, the Apple Watch smartwatch, and the Apple TV digital media player. Apple&#039;s consumer software includes the macOS and iOS operating systems, the iTunes media player, the Safari web browser, and the iLife and iWork creativity and productivity suites. Its online services include the iTunes Store, the iOS App Store and Mac App Store, and iCloud.', 'P',  0,  'R',  '2016-09-27', '2017-09-03', '2016-09-27', 'www',  '0',  'A',  '', 0.00, 0,  '', 18, 0,  0,  '0',  '0',  '2017-09-03', 'apple inc is an american multinational technology company headquartered in cupertino california that designs develops and sells consumer electronics computer software online services its hardware products include the iphone smartphone ipad tablet mac personal ipod portable media player watch smartwatch tv digital apples includes macos ios operating systems itunes safari web browser ilife iwork creativity productivity suites store app icloud campus us '),
(4, 1,  'admin',  'admin@admin.com',  '', 1,  0,  0,  0,  0,  0,  'google.php', 'Google', 'https://www.google.com', '', '', 'Google is an American multinational technology company specializing in Internet-related services and products that include online advertising technologies, search, cloud computing, and software. Most of its profits are derived from AdWords, an online advertising service that places advertising near the list of search results.',  'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 7,  0,  0,  '0',  '0',  '2017-09-03', 'google is an american multinational technology company specializing in internetrelated services and products that include online advertising technologies search cloud computing software most of its profits are derived from adwords service places near the list results googleplex mountain view california us '),
(5, 1,  'admin',  'admin@admin.com',  '', 1,  0,  0,  0,  0,  0,  'c97net.php', 'C97net', 'http://www.c97.net', '', '', 'C97net is an Indonesia based technology company headquartered in Bandung that develops, manufactures, licenses, supports and sells computer software, consumer electronics and personal computers and services. Its best known software products are the qEngine, Cart Engine &amp; Kemana.\r\n\r\nThis listing is sponsored, it always be displayed first in list mode with different background and a special custom field.',  'P',  5,  'S',  '2016-09-27', '0000-00-00', '2025-10-01', '', '0',  'A',  '', 5.00, 1,  '', 13, 0,  0,  '0',  '0',  '2017-09-03', 'c97net is an indonesia based technology company headquartered in bandung that develops manufactures licenses supports and sells computer software consumer electronics personal computers services its best known products are the qengine cart engine amp kemana this listing sponsored it always be displayed first list mode with different background a special custom field jln caringin '),
(6, 1,  'admin',  'admin@admin.com',  '', 8,  0,  0,  0,  0,  0,  'contoso-ltd.php',  'Contoso Ltd',  'https://www.microsoft.com',  '', '', 'Contoso Ltd. (also known as Contoso and Contoso University) is a fictional company used by Microsoft as an example company and domain.\r\n\r\nThis also demonstrate Kemana duplicate URL detection.',  'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 2,  0,  0,  '0',  '0',  '2017-09-03', 'contoso ltd also known as and university is a fictional company used by microsoft an example domain this demonstrate kemana duplicate url detection '),
(7, 2,  'admin',  'admin@admin.com',  '', 9,  0,  0,  0,  0,  0,  'audi-q3-1-4tfsi-turbo-white-2014.php', 'Audi Q3 1.4TFSi Turbo White 2014', '', '', '', 'Audi AG is a German automobile manufacturer that designs, engineers, produces, markets and distributes luxury vehicles. Audi oversees worldwide operations from its headquarters in Ingolstadt, Bavaria, Germany.',  'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 16, 0,  0,  '0',  '0',  '2017-09-03', 'audi q3 14tfsi turbo white 2014 ag is a german automobile manufacturer that designs engineers produces markets and distributes luxury vehicles oversees worldwide operations from its headquarters in ingolstadt bavaria germany tiptronic '),
(9, 2,  'admin',  'admin@admin.com',  '', 10, 0,  0,  0,  0,  0,  'bmw-320i-luxury-2015-black.php', 'BMW 320i Luxury 2015 Black', '', '', '', 'Bayerische Motoren Werke AG, usually known under its abbreviation BMW, is a German luxury vehicles, motorcycle, and engine manufacturing company founded in 1916. Headquartered in Munich, Bavaria, Germany.', 'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 3,  0,  0,  '0',  '0',  '2017-09-03', 'bmw 320i luxury 2015 black bayerische motoren werke ag usually known under its abbreviation is a german vehicles motorcycle and engine manufacturing company founded in 1916 headquartered munich bavaria germany automatic '),
(10,  2,  'admin',  'admin@admin.com',  '', 11, 0,  0,  0,  0,  0,  'ferrari-458-spider-2012.php',  'Ferrari 458 Spider 2012',  '', '', '', 'Ferrari S.p.A. is an Italian sports car manufacturer based in Maranello. Founded by Enzo Ferrari in 1939 as Auto Avio Costruzioni, the company built its first car in 1940.',  'P',  0,  'P',  '2016-09-27', '0000-00-00', '2025-09-27', '', '0',  'A',  '', 0.00, 0,  '', 20, 0,  0,  '0',  '0',  '2017-09-03', 'ferrari 458 spider 2012 spa is an italian sports car manufacturer based in maranello founded by enzo 1939 as auto avio costruzioni the company built its first 1940 automatic '),
(11,  2,  'admin',  'admin@admin.com',  '', 12, 0,  0,  0,  0,  0,  'honda-civic-type-r-2015.php',  'Honda Civic Type R 2015',  '', '', '', 'Honda Motor Co., Ltd. is a Japanese public multinational conglomerate corporation primarily known as a manufacturer of automobiles, aircraft, motorcycles, and power equipment.',  'P',  0,  'R',  '2016-09-27', '0000-00-00', '2016-09-27', '', '0',  'A',  '', 0.00, 0,  '', 5,  0,  0,  '0',  '0',  '2017-09-03', 'honda civic type r 2015 motor co ltd is a japanese public multinational conglomerate corporation primarily known as manufacturer of automobiles aircraft motorcycles and power equipment manual ');

INSERT INTO `__PREFIX__listing_cat` (`parent_id`, `idx`, `dir_id`, `cat_name`, `cat_details`, `cat_image`, `cat_keywords`, `cat_featured`, `cat_page`, `cat_num_link`, `menu_idx`, `menu_item_id`, `permalink`, `is_removed`) VALUES
(0,	1,	1,	'Default Category',	'<p>This is a dummy category, you can remove it from ACP.</p>',	'',	'',	'',	'',	5,	6,	14,	'category/default-category.php',	''),
(0,	2,	1,	'Another Category',	'',	'',	'',	'',	'',	1,	6,	18,	'category/another-category.php',	''),
(0,	3,	1,	'Yet Another Category',	'',	'',	'',	'',	'',	0,	6,	19,	'category/yet-another-category.php',	''),
(0,	4,	1,	'And another',	'',	'',	'',	'',	'',	0,	6,	20,	'category/and-another.php',	''),
(2,	5,	1,	'Deep Category Demo',	'',	'',	'',	'',	'',	1,	6,	22,	'category/deep-category-demo.php',	''),
(5,	6,	1,	'Quite Deep',	'',	'',	'',	'',	'',	1,	6,	23,	'category/quite-deep.php',	''),
(6,	7,	1,	'Very Deep',	'',	'',	'',	'',	'',	1,	6,	24,	'category/very-deep.php',	''),
(7,	8,	1,	'Really Deep',	'',	'',	'',	'',	'',	1,	6,	25,	'category/really-deep.php',	''),
(0,	9,	2,	'Audi',	'',	'',	'',	'',	'',	1,	8,	31,	'category/audi.php',	''),
(0,	10,	2,	'BMW',	'',	'',	'',	'',	'',	1,	8,	32,	'category/bmw.php',	''),
(0,	11,	2,	'Ferrari',	'',	'',	'',	'',	'',	1,	8,	33,	'category/ferrari.php',	''),
(0,	12,	2,	'Honda',	'',	'',	'',	'',	'',	1,	8,	34,	'category/honda.php',	''),
(0,	13,	2,	'Mercedes-Benz',	'',	'',	'',	'',	'',	0,	8,	35,	'category/mercedes-benz.php',	''),
(0,	14,	2,	'Mitsubishi',	'',	'',	'',	'',	'',	0,	8,	36,	'category/mitsubishi.php',	''),
(0,	15,	2,	'Porsche',	'',	'',	'',	'',	'',	0,	8,	37,	'category/porsche.php',	''),
(0,	16,	2,	'Toyota',	'',	'',	'',	'',	'',	0,	8,	38,	'category/toyota.php',	'');

INSERT INTO `__PREFIX__listing_cf_define` (`idx`, `dir_id`, `cf_title`, `cf_type`, `cf_option`, `cf_help`, `is_searchable`, `is_required`, `is_list`, `avail_to`, `menu_idx`, `menu_item_id`, `is_removed`) VALUES
(1,	1,	'Employees',	'varchar',	'',	'',	'0',	'0',	'1',	'R,P,S',	7,	15,	''),
(2,	1,	'Address',	'textarea',	'',	'',	'1',	'0',	'0',	'R,P,S',	7,	16,	''),
(3,	1,	'Map',	'gmap',	'',	'',	'0',	'0',	'0',	'R,P,S',	7,	17,	''),
(4,	1,	'Motto',	'varchar',	'',	'',	'0',	'0',	'1',	'S',	7,	21,	''),
(5,	2,	'Contact Number',	'tel',	'',	'',	'0',	'1',	'0',	'R,P,S',	9,	27,	''),
(6,	2,	'Price',	'varchar',	'',	'',	'0',	'1',	'1',	'R,P,S',	9,	28,	''),
(7,	2,	'Transmission',	'select',	'Automatic\r\nManual\r\nTiptronic',	'',	'1',	'1',	'0',	'R,P,S',	9,	29,	''),
(8,	2,	'Year',	'varchar',	'',	'',	'1',	'1',	'0',	'R,P,S',	9,	30,	''),
(9,	2,	'Condition',	'rating',	'',	'',	'1',	'1',	'1',	'R,P,S',	9,	39,	''),
(10,	2,	'More Image',	'img',	'',	'',	'0',	'0',	'0',	'R,P,S',	9,	40,	''),
(11,	2,	'More Image (2)',	'img',	'',	'',	'0',	'0',	'0',	'R,P,S',	9,	41,	''),
(12,  2,  'Video',  'video',  '', 'Link your ads video',  '0',  '0',  '1',  'R,P,S',  9,  43, '');

INSERT INTO `qe_listing_cf_value` (`idx`, `item_id`, `cf_1`, `cf_2`, `cf_3`, `cf_4`, `cf_5`, `cf_6`, `cf_7`, `cf_8`, `cf_9`, `cf_10`, `cf_11`, `cf_12`) VALUES
(1, 1,  '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 2,  '114,000',  'One Microsoft Way\r\nRedmond, Washington\r\nU.S',  '47.6393225,-122.1283833',  '', '', '', '', '', '', '', '', ''),
(3, 3,  '115,000',  'Apple Campus\r\nCupertino, California\r\nU.S.',  '37.33182,-122.03118',  '', '', '', '', '', '', '', '', ''),
(4, 4,  '57,000', 'Googleplex\r\nMountain View, California\r\nU.S', '37.4192684,-122.07869440000002', '', '', '', '', '', '', '', '', ''),
(5, 5,  '1',  'Jln. Caringin\r\nBandung\r\nIndonesia',  '-6.945767821924124,107.58383274078369',  'Be Unique. Be Different.', '', '', '', '', '', '', '', ''),
(6, 6,  '12,000', '', '', '', '', '', '', '', '', '', '', ''),
(7, 7,  '', '', '', '', '555-12345',  '$50,000',  'Tiptronic',  '2014', '4',  'au009161web.jpg',  'AU009172WEB.jpg',  ''),
(9, 9,  '', '', '', '', '555-23456',  '$55,000',  'Automatic',  '2015', '5',  'Mary-Kay-2013-BMW-320i.jpg', 'bmw-320i-black-2015-wallpap.jpg',  ''),
(10,  10, '', '', '', '', '555-34567',  '$250,000', 'Automatic',  '2012', '5',  '2012-ferrari-458-spider-photo-424402-s-1280x782.jpg',  '2012-ferrari-458-spider-photo-424432-s-1280x782.jpg',  ''),
(11,  11, '', '', '', '', '555-45678',  '$15,000',  'Manual', '2015', '4',  'maxresdefault.jpg',  'Honda-Civic-Type-R-2015-09.jpg', 'https://www.youtube.com/watch?v=MP06gvFWW64');

INSERT INTO `__PREFIX__listing_dir` (`idx`, `dir_short`, `dir_title`, `dir_permalink`, `dir_image`, `dir_body`, `dir_backlink`, `dir_summary`, `dir_url`, `dir_url_mask`, `dir_multi_cat`, `dir_no_detail`, `dir_per_page`, `dir_logo`, `dir_logo_size`, `dir_comment`, `dir_default_sort`, `dir_default_view`, `dir_pre_allow`, `dir_spo_allow`, `dir_pre_fee`, `dir_spo_fee`, `dir_cat_menu_id`, `dir_cf_menu_id`, `dir_featured`, `dir_default`, `menu_item_id`, `is_removed`) VALUES
(1, 'gen',  'Company Directory',  'company-directory.php',  'shutterstock_47871322.jpg',  '<p>List of companies around the world. Using the following features:</p>\r\n<p>Logo Upload, user ratings, Custom Field for employees, address &amp; map. Employees informations are visible in search result. Address is searchable &amp; map is using google maps.</p>\r\n<p>Suspendisse euismod luctus turpis a varius. Integer sit amet congue dolor.</p>', '1',  '0',  '1',  '1',  1,  '0',  0,  '1',  0,  '1',  'dd', 'grid', '1',  '1',  5.00, 10.00,  6,  7,  '5',  '1',  13, ''),
(2, 'car',  'Car For Sale', 'car-for-sale.php', 'autofinancing_used_car.jpg', '<p>Kemana can also be used for classified ad! Used features:</p>\r\n<p>Logo upload, no user comments, default view: list, custom fields: price, condition, transmissions, year, more images.</p>\r\n<p>Phasellus vel mauris eget massa consequat semper. Maecenas vel molestie eros, nec semper sapien. Mauris vitae tincidunt augue.</p>',  '0',  '0',  '0',  '0',  1,  '0',  0,  '1',  0,  '0',  'dd', 'list', '1',  '1',  5.00, 10.00,  8,  9,  '', '0',  26, '');

INSERT INTO `__PREFIX__menu_item` (`idx`, `menu_id`, `menu_parent`, `menu_item`, `menu_url`, `menu_permalink`, `menu_order`, `ref_idx`) VALUES
(1, 2,  0,  'Contact Us', '__SITE__/contact.php', '', 110,  0),
(2, 2,  0,  'Site Map', '__SITE__/sitemap.php', '', 100,  0),
(4, 2,  0,  'Tell a Friend',  '__SITE__/tell.php',  '', 120,  0),
(5, 3,  0,  'Privacy Policy', '2',  '', 100,  0),
(6, 3,  0,  'Terms &amp; Conditions', '7',  '', 110,  0),
(7, 3,  0,  'Powered by qEngine', '8',  '', 120,  0),
(8, 3,  0,  'FAQ&#039;s', '3',  '', 130,  0),
(9, 3,  0,  'About Us', '4',  '', 140,  0),
(10,  1,  0,  'Home', '__SITE__', '', 100,  0),
(11,  1,  0,  'Add Listing',  '__SITE__/add.php', '', 110,  0),
(12,	1,	0,	'#',	'[[sm:dir_menu]]',	'',	120,	0),
(13,	5,	0,	'Company Directory',	'__SITE__/index.php?dir_id=1',	'__SITE__/company-directory.php',	100,	1),
(14,	6,	0,	'Default Category',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=1',	'__SITE__/category/default-category.php',	100,	1),
(15,	7,	0,	'Employees',	'',	'',	100,	1),
(16,	7,	0,	'Address',	'',	'',	110,	2),
(17,	7,	0,	'Map',	'',	'',	120,	3),
(18,	6,	0,	'Another Category',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=2',	'__SITE__/category/another-category.php',	110,	2),
(19,	6,	0,	'Yet Another Category',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=3',	'__SITE__/category/yet-another-category.php',	160,	3),
(20,	6,	0,	'And another',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=4',	'__SITE__/category/and-another.php',	170,	4),
(21,	7,	0,	'Motto',	'',	'',	130,	4),
(22,	6,	18,	'Deep Category Demo',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=5',	'__SITE__/category/deep-category-demo.php',	120,	5),
(23,	6,	22,	'Quite Deep',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=6',	'__SITE__/category/quite-deep.php',	130,	6),
(24,	6,	23,	'Very Deep',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=7',	'__SITE__/category/very-deep.php',	140,	7),
(25,	6,	24,	'Really Deep',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=8',	'__SITE__/category/really-deep.php',	150,	8),
(26,	5,	0,	'Car For Sale',	'__SITE__/index.php?dir_id=2',	'__SITE__/car-for-sale.php',	110,	2),
(27,	9,	0,	'Contact Number',	'',	'',	100,	5),
(28,	9,	0,	'Price',	'',	'',	110,	6),
(29,	9,	0,	'Transmission',	'',	'',	120,	7),
(30,	9,	0,	'Year',	'',	'',	130,	8),
(31,	8,	0,	'Audi',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=9',	'__SITE__/category/audi.php',	100,	9),
(32,	8,	0,	'BMW',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=10',	'__SITE__/category/bmw.php',	110,	10),
(33,	8,	0,	'Ferrari',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=11',	'__SITE__/category/ferrari.php',	120,	11),
(34,	8,	0,	'Honda',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=12',	'__SITE__/category/honda.php',	130,	12),
(35,	8,	0,	'Mercedes-Benz',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=13',	'__SITE__/category/mercedes-benz.php',	140,	13),
(36,	8,	0,	'Mitsubishi',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=14',	'__SITE__/category/mitsubishi.php',	150,	14),
(37,	8,	0,	'Porsche',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=15',	'__SITE__/category/porsche.php',	160,	15),
(38,	8,	0,	'Toyota',	'__SITE__/listing_search.php?cmd=list&amp;cat_id=16',	'__SITE__/category/toyota.php',	170,	16),
(39,	9,	0,	'Condition',	'',	'',	140,	9),
(40,	9,	0,	'More Image',	'',	'',	150,	10),
(41,	9,	0,	'More Image (2)',	'',	'',	160,	11),
(42,  2,  0,  'Backlinking',  '14', '', 130,  0),
(43,  2,  0,  'Video',  '', '', 170,  12);

INSERT INTO `__PREFIX__menu_set` (`idx`, `menu_id`, `menu_title`, `menu_preset`, `menu_class`, `menu_notes`, `menu_cache`, `menu_locked`, `ref_idx`) VALUES
(1, 'main_menu',  'Main Menu',  'bsnav',  '', 'Main menu, usually located at the top of the page.', '<ul id=\"qmenu_main_menu\" class=\"nav navbar-nav\">\n <li><a href=\"__SITE__\">Home</a></li>\n  <li><a href=\"__SITE__/add.php\">Add Listing</a></li>\n <li><a href=\"[[sm:dir_menu]]\">#</a></li>\n</ul>\n', '0',  0),
(2, 'footer_menu',  'Footer Menu',  'list_1', '', 'Footer menu, usually located at the end of the page.', '<ul id=\"qmenu_footer_menu\" class=\"list_1\">\n <li><a href=\"__SITE__/sitemap.php\">Site Map</a></li>\n  <li><a href=\"__SITE__/contact.php\">Contact Us</a></li>\n  <li><a href=\"__SITE__/tell.php\">Tell a Friend</a></li>\n  <li><a href=\"__SITE__/backlinking-instructions.php\">Backlinking</a></li>\n</ul>\n', '0',  0),
(3, 'page_menu',  'Page Menu',  'list_1', '', 'Menu linking to some important, but not that important pages.',  '<ul id=\"qmenu_page_menu\" class=\"list_1\">\n <li><a href=\"__SITE__/privacy-policy.php\">Privacy Policy</a></li>\n <li><a href=\"__SITE__/terms-and-conditions.php\">Terms & Conditions</a></li>\n <li><a href=\"__SITE__/powered-by-qengine.php\">Powered by qEngine</a></li>\n <li><a href=\"__SITE__/faqs.php\">FAQ\'s</a></li>\n <li><a href=\"__SITE__/about-us.php\">About Us</a></li>\n</ul>\n',  '0',  0),
(5, 'dir_menu', 'Multiple Directories Menu',  'sf-menu',  '', 'Multiple directories menu. Do NOT remove!',  '<ul id=\"qmenu_dir_menu\" class=\"sf-menu\">\n <li><a href=\"__SITE__/company-directory.php\">Company Directory</a></li>\n <li><a href=\"__SITE__/car-for-sale.php\">Car For Sale</a></li>\n</ul>\n',  '1',  0),
(6, 'dir_cat_gen',  'Categories for General Directory', 'dir_cat.1',  '', 'Container for category menu of General Directory. Do NOT remove!', '<ul id=\"qmenu_dir_cat_gen\" class=\"dir_cat.1\">\n  <li><a href=\"__SITE__/category/default-category.php\">Default Category</a></li>\n  <li><a href=\"__SITE__/category/another-category.php\">Another Category</a>\n <ul>\n    <li><a href=\"__SITE__/category/deep-category-demo.php\">Deep Category Demo</a>\n   <ul>\n      <li><a href=\"__SITE__/category/quite-deep.php\">Quite Deep</a>\n     <ul>\n        <li><a href=\"__SITE__/category/very-deep.php\">Very Deep</a>\n       <ul>\n          <li><a href=\"__SITE__/category/really-deep.php\">Really Deep</a></li>\n        </ul>\n     </li>\n     </ul>\n   </li>\n   </ul>\n </li>\n </ul>\n</li>\n  <li><a href=\"__SITE__/category/yet-another-category.php\">Yet Another Category</a></li>\n  <li><a href=\"__SITE__/category/and-another.php\">And another</a></li>\n</ul>\n', '1',  0),
(7, 'dir_cf_gen', 'Custom field for General Directory', 'dir_cf.1', '', 'Container for custom field menu of General Directory. Do NOT remove!', '<ul id=\"qmenu_dir_cf_gen\" class=\"dir_cf.1\">\n  <li>Employees</li>\n  <li>Address</li>\n  <li>Map</li>\n  <li>Motto</li>\n</ul>\n', '1',  0),
(8, 'dir_cat_car',  'Categories for Car For Sale',  'dir_cat.2',  '', 'Container for category menu of Car For Sale. Do NOT remove!',  '<ul id=\"qmenu_dir_cat_car\" class=\"dir_cat.2\">\n  <li><a href=\"__SITE__/category/audi.php\">Audi</a></li>\n  <li><a href=\"__SITE__/category/bmw.php\">BMW</a></li>\n  <li><a href=\"__SITE__/category/ferrari.php\">Ferrari</a></li>\n  <li><a href=\"__SITE__/category/honda.php\">Honda</a></li>\n  <li><a href=\"__SITE__/category/mercedes-benz.php\">Mercedes-Benz</a></li>\n  <li><a href=\"__SITE__/category/mitsubishi.php\">Mitsubishi</a></li>\n  <li><a href=\"__SITE__/category/porsche.php\">Porsche</a></li>\n  <li><a href=\"__SITE__/category/toyota.php\">Toyota</a></li>\n</ul>\n', '1',  0),
(9, 'dir_cf_car', 'Custom field for Car For Sale',  'dir_cf.2', '', 'Container for custom field menu of Car For Sale. Do NOT remove!',  '<ul id=\"qmenu_dir_cf_car\" class=\"dir_cf.2\">\n  <li>Contact Number</li>\n <li>Price</li>\n  <li>Transmission</li>\n <li>Year</li>\n <li>Condition</li>\n  <li>More Image</li>\n <li>More Image (2)</li>\n <li>Video</li></ul>\n',  '1',  0);

INSERT INTO `__PREFIX__module` (`mod_id`, `mod_type`, `mod_name`, `mod_desc`, `mod_version`, `mod_css`, `mod_js`, `mod_enabled`) VALUES
('ztopwatch', 'general',  'Ztopwatch',  'This module replace the old stopwatch in qEngine 1', '1.0.0',  '', '', '1'),
('qbanner', 'general',  'qBanner',  'Use this module to upload & display banners.', '1.0.0',  '', '', '1'),
('page_gallery',  'general',  'Page Gallery', 'Display selected pages or categories or groups anywhere!', '2.0.0',  '', '', '1'),
('box', 'general',  'Box',  'A simple module to display static html for your page, without editing .tpl files, best used with qE 4.x\'s Module Manager.', '1.0.0',  '', '', '1'),
('qcomment',  'general',  'qComment', 'Add user comments & user ratings to your site and your modules, easily!',  '3.0.0',  '', '', '1'),
('qmenu', 'general',  'qMenu',  'Use qMenu module to display your designed menu easily!', '1.0.0',  '', '', '1'),
('slideshow', 'general',  'Slideshow',  'This module to replace qEngine\'s old featured contents.', '1.0.0',  'slideshow.css',  '', '1'),
('qstats',  'general',  'Simple Stats', 'This module replaces qEngine\'s old simple statistics of visitors\' hits & visits.', '1.0.0',  '', '', '1'),
('ke_core', 'general',  'Kemana 2 Core Module', 'This is a multipurpose module for Kemana 2. This module contains support for item listings and custom fields.',  '1.0.0',  'module_ke_core.css', '', '1'),
('pay_paypal',  'payment',  'PayPal', 'A payment gateway for PayPal (IPN). (Also contains information for developers)', '1.0.0',  '', '', '1'),
('pay_bank',  'payment',  'Bank Wire Transfer', 'A payment gateway for Bank Wire Transfer.',  '1.0.0',  '', '', '1'),
('pay_cheque',  'payment',  'Cheque', 'A payment gateway for By Cheque.', '1.0.0',  '', '', '1');

INSERT INTO `__PREFIX__module_pos` (`idx`, `mod_id`, `mod_title`, `mod_config`, `mod_pos`) VALUES
(1, 'box',  'Add Anything!',  '&lt;p&gt;You can easily adds any HTML or JavaScript tags by editing this module, or create a new box, by using Box Module in ACP &gt; Modules &gt; Layout.&lt;/p&gt;\r\n\r\n&lt;p&gt;Add Google AdSense, Facebook Feeds, Twitter Updates, by editing this module.&lt;/p&gt;',  'R1'),
(2, 'box',  'Info Box', '&lt;p&gt;Manage this module from ACP. Display up to 40 modules easily!&lt;/p&gt;\r\n\r\n&lt;p&gt;You can also remove this information from Module Management.&lt;/p&gt;\r\n\r\n&lt;p&gt;In default skin, this module appears on the right.&lt;/p&gt;', 'L2'),
(4, 'qmenu',  'Tools',  'menu=footer_menu', 'B1'),
(5, 'qmenu',  'Pages',  'menu=page_menu', 'B1'),
(3, 'qbanner',  'Banner', '', 'L1'),
(6, 'qmenu',  'qMenu',  'menu=main_menu', 'T2'),
(9, 'qstats', 'Simple Stats', '', 'R2');

INSERT INTO `__PREFIX__page` (`group_id`, `cat_id`, `page_id`, `permalink`, `page_image`, `page_date`, `page_time`, `page_unix`, `page_title`, `page_keyword`, `page_body`, `page_author`, `page_related`, `page_allow_comment`, `last_update`, `page_rating`, `page_comment`, `page_list`, `page_hit`, `page_img_tmp`, `page_attachment`, `page_attachment_stat`, `page_pinned`, `page_status`, `page_template`, `page_mode`) VALUES
('GENPG', 1,  1,  'welcome.php',  '', '2011-11-11', '14:15:00', 1321017300, 'Welcome',  'add,your,keyword,here,qengine,c97net', '<p>Welcome to our site, please enjoy your stay here. If you have any question, please contact us.</p>\r\n<p>This is an example of a page, you could edit this to put information about yourself or your site so readers know where you are coming from. As mentioned before, you can create as many pages like this one.</p>\r\n<p>You can edit this text in Admin &gt; Page Manager.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent placerat eros vitae dolor pellentesque at tristique sapien vehicula. Mauris sed ligula enim, hendrerit viverra turpis. Sed pretium pharetra convallis. Fusce ac velit eget libero dapibus vehicula. Etiam ullamcorper congue odio eget porta. Praesent accumsan, dui ac condimentum congue, nisi odio auctor enim, a aliquam est enim condimentum risus. Sed in nisl placerat magna mattis auctor eu in erat.</p>\r\n<p>This is an example of a page, you could edit this to put information about yourself so readers know where you are coming from. You can create as many pages like this one.</p>\r\n<p>Aenean pellentesque metus at purus pretium sed vehicula tortor aliquam. Curabitur erat turpis, rhoncus et molestie id, pharetra non turpis. Pellentesque ultricies, urna sed lobortis tincidunt, dolor orci dapibus lectus, in aliquet ipsum sapien ac tortor. Suspendisse eu ante nibh, vel aliquam magna. Proin vel felis libero. Nullam mauris neque, suscipit sit amet placerat rutrum, vestibulum sed mauris. Sed magna nulla, tristique sit amet volutpat sed, cursus sit amet purus. Nam lobortis odio eu nunc fermentum adipiscing. Pellentesque congue ornare ipsum venenatis porta. Aenean scelerisque porttitor metus, a ornare ante rhoncus nec. Etiam metus risus, porttitor luctus iaculis sed, elementum in odio. Etiam metus leo, interdum vitae gravida sit amet, tincidunt id velit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>',  'admin',  '', '0',  1447165085, 0.00, 0,  '0',  111,  '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  2,  'privacy-policy.php', '', '2011-11-11', '14:30:00', 1320996600, 'Privacy Policy', '', '<h2>Information that is gathered from visitors</h2>\r\n<p>In common with other websites, log files are stored on the web server saving details such as the visitor\'s IP address, browser type, referring page and time of visit.</p>\r\n<p>Cookies may be used to remember visitor preferences when interacting with the website.</p>\r\n<p>Where registration is required, the visitor\'s email and a username will be stored on the server.</p>\r\n<h2>How the Information is used</h2>\r\n<p>The information is used to enhance the vistor\'s experience when using the website to display personalised content and possibly advertising.</p>\r\n<p>E-mail addresses will not be sold, rented or leased to 3rd parties.</p>\r\n<p>E-mail may be sent to inform you of news of our services or offers by us or our affiliates.</p>\r\n<h2>Visitor Options</h2>\r\n<p>If you have subscribed to one of our services, you may unsubscribe by following the instructions which are included in e-mail that you receive.</p>\r\n<p>You may be able to block cookies via your browser settings but this may prevent you from access to certain features of the website.</p>\r\n<h2>Cookies</h2>\r\n<p>Cookies are small digital signature files that are stored by your web browser that allow your preferences to be recorded when visiting the website. Also they may be used to track your return visits to the website.</p>\r\n<p>3rd party advertising companies may also use cookies for tracking purposes.</p>\r\n<h2>Google Ads</h2>\r\n<p>Google, as a third party vendor, uses cookies to serve ads.</p>\r\n<p>Google\'s use of the DART cookie enables it to serve ads to visitors based on their visit to sites they visit on the Internet.</p>\r\n<p>Website visitors may opt out of the use of the DART cookie by visiting the Google ad and content network privacy policy.</p>\r\n<p>(last updated March 2009)<br />Based on <a href=\"http://www.freeprivacypolicy.org/\">FPP</a></p>', 'admin',  '', '1',  1366442573, 0.00, 0,  '1',  81, '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  3,  'faqs.php', '', '2011-11-11', '00:00:00', 1320944400, 'FAQ&#039;s', '', '<p>Here you will find answers to many of your questions. If there is something which you cannot find the answer to, let us know and we will add your question to this list.</p>',  'admin',  '', '1',  1366442718, 0.00, 0,  '1',  15, '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  4,  'about-us.php', '', '2011-11-11', '00:00:00', 1320966000, 'About Us', '', '<p>Tell who you are, what you do, and anything else. Company history and the chairman will be a nice addition.</p>', 'admin',  '', '1',  1443117458, 0.00, 0,  '1',  341,  '', '', 3,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  6,  'contact-us.php', '', '2011-11-11', '00:00:00', 1320944400, 'Contact Us', '', '<p>Put your contact information here, such as office hour, parking spot, direction, etc.</p>\r\n<p>Change this text in Admin &gt; Page Manager</p>', 'admin',  '', '0',  1366442727, 0.00, 0,  '0',  0,  '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  7,  'terms-and-conditions.php', '', '2011-11-11', '00:00:00', 1320944400, 'Terms &amp; Conditions', '', '<p>Welcome to our website. If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern [business name]\'s relationship with you in relation to this website. If you disagree with any part of these terms and conditions, please do not use our website.</p>\r\n<p>The term \'[business name]\' or \'us\' or \'we\' refers to the owner of the website whose registered office is [address]. Our company registration number is [company registration number and place of registration]. The term \'you\' refers to the user or viewer of our website.</p>\r\n<p>The use of this website is subject to the following terms of use:</p>\r\n<ul>\r\n<li>The content of the pages of this website is for your general information and use only. It is subject to change without notice.</li>\r\n<li>This website uses cookies to monitor browsing preferences. If you do allow cookies to be used, the following personal information may be stored by us for use by third parties: [insert list of information].</li>\r\n<li>Neither we nor any third parties provide any warranty or guarantee as to the accuracy, timeliness, performance, completeness or suitability of the information and materials found or offered on this website for any particular purpose. You acknowledge that such information and materials may contain inaccuracies or errors and we expressly exclude liability for any such inaccuracies or errors to the fullest extent permitted by law.</li>\r\n<li>Your use of any information or materials on this website is entirely at your own risk, for which we shall not be liable. It shall be your own responsibility to ensure that any products, services or information available through this website meet your specific requirements.</li>\r\n<li>This website contains material which is owned by or licensed to us. This material includes, but is not limited to, the design, layout, look, appearance and graphics. Reproduction is prohibited other than in accordance with the copyright notice, which forms part of these terms and conditions.</li>\r\n<li>All trade marks reproduced in this website which are not the property of, or licensed to, the operator are acknowledged on the website.</li>\r\n<li>Unauthorised use of this website may give rise to a claim for damages and/or be a criminal offence.</li>\r\n<li>From time to time this website may also include links to other websites. These links are provided for your convenience to provide further information. They do not signify that we endorse the website(s). We have no responsibility for the content of the linked website(s).</li>\r\n<li>Your use of this website and any dispute arising out of such use of the website is subject to the laws of England, Northern Ireland,&nbsp;Scotland and Wales.</li>\r\n</ul>',  'admin',  '', '1',  1366442731, 0.00, 0,  '1',  52, '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('GENPG', 1,  8,  'powered-by-qengine.php', '', '2011-11-11', '00:00:00', 1320944400, 'Powered by qEngine', '', '<p>This site is using qEngine CMS, a simple CMS engine created by <a href=\"http://www.c97.net\">C97.net</a>. qEngine is very easy to use &amp; maintain, no need to understand PHP, and it\'s <strong>FREE!</strong> If you are interested to use this awesome script please visit <a href=\"http://www.c97.net\">www.c97.net</a> now!</p>',  'admin',  '', '1',  1366442735, 0.00, 0,  '1',  26, '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('NEWS',  2,  9,  'news/site-is-now-up-and-ready.php',  '', '2011-11-11', '00:00:00', 1320966000, 'Site is Now Up &amp; Ready', '', '<h2>Congratulations!</h2>\r\n<p>You have succesfully installed qEngine!<br />To learn more about qEngine, please visit <a href=\"http://www.c97.net\" target=\"_blank\">www.c97.net</a>.<br /><br />(To remove this message, go to ACP &gt; Contents &gt; Manage Contents)</p>', 'admin',  '', '0',  1469804396, 0.00, 0,  '1',  94, '', '', 0,  '0',  'P',  'page_default.tpl', 'html'),
('QBANR', 0,  10, '', 'banner2.jpg',  '0000-00-00', '00:00:00', 0,  'The Banner', '#',  'This page is part of qBanner module. Please use qBanner Manager to edit this page.', '', '', '', 0,  0.00, 0,  '', 0,  '', '', 0,  '0',  'P',  '', 'html'),
('SSHOW', 0,  11, '', 'slide1.jpg', '0000-00-00', '00:00:00', 0,  'Change this content from ACP', '#',  'This page is part of SlideShow module. Please use SlideShow Manager to edit this page.', '', '', '', 0,  0.00, 0,  '', 0,  '', '', 0,  '0',  'P',  '', 'html'),
('SSHOW', 0,  12, '', 'slide2.jpg', '0000-00-00', '00:00:00', 0,  'The Mountain', '#',  'This page is part of SlideShow module. Please use SlideShow Manager to edit this page.', '', '', '', 0,  0.00, 0,  '', 0,  '', '', 0,  '0',  'P',  '', 'html'),
('SSHOW', 0,  13, '', 'slide3.jpg', '0000-00-00', '00:00:00', 0,  'The city', '#',  'This page is part of SlideShow module. Please use SlideShow Manager to edit this page.', '', '', '', 0,  0.00, 0,  '', 0,  '', '', 0,  '0',  'P',  '', 'html'),
('GENPG', 1,  5,  'transaction-success.php',  '', '2011-11-11', '00:00:00', 1320966000, 'Transaction Success',  '', '<p>Thank you for purchasing in our site.</p>', '', '', '0',  1473179378, 0.00, 0,  '0',  0,  '', '', 0,  '0',  'P',  '', 'html'),
('GENPG', 1,  14, 'backlinking-instructions.php', '', '2017-04-01', '18:10:00', 1491063000, 'Backlinking Instructions', '', '<p>This web site may need your listing to have a backlink to this web site. Please add the following code to your web site:</p>\r\n<pre>{qemod:ke_core:backlink}</pre>\r\n<p>Replace [user_id] with your user ID. If you are already logged in, simply copy the code. Please don\'t change the code, or we can not validate your backlink.</p>\r\n<p>After you add the code, please update your listings.</p>\r\n<p>PS: for administrator, if your web site doesn\'t need any backlinking, you may want to remove this information. You can change the backlink code in ACP - Settings.</p>',  'admin',  '', '1',  1491063767, 0.00, 0,  '1',  18, '', '', 0,  '0',  'P',  'page_default.tpl', 'html');

INSERT INTO `__PREFIX__page_cat` (`idx`, `group_id`, `parent_id`, `permalink`, `cat_name`, `cat_details`, `cat_image`) VALUES
(1, 'GENPG',  0,  'general-pages.php',  'General Pages',  '<p>General Pages</p>', ''),
(2, 'NEWS', 0,  'news.php', 'General News', '<p>General News</p>',  '');

INSERT INTO `__PREFIX__page_group` (`idx`, `group_id`, `group_title`, `group_notes`, `all_cat_list`, `cat_list`, `page_cat`, `page_image`, `page_image_size`, `page_thumb`, `page_gallery`, `page_gallery_thumb`, `page_author`, `page_comment`, `page_attachment`, `page_date`, `page_folder`, `page_sort`, `hidden_private`, `group_template`, `page_template`) VALUES
(1, 'GENPG',  'Common Page',  'General pages, eg: company history, about you, etc. (please do NOT remove this content type, you can edit this message in ACP &gt; Contents &gt; Manage Types)', '1',  '1',  '1',  '1',  500,  100,  '1',  200,  '1',  'pagecomment',  '0',  '0',  '', 't',  '0',  'body_default.tpl', 'page_default.tpl'),
(2, 'NEWS', 'News', 'Site news (please do NOT remove this content type, you can edit this message in ACP &gt; Contents &gt; Manage Types)', '1',  '1',  '1',  '1',  400,  100,  '0',  0,  '0',  '0',  '0',  '1',  'news', 't',  '0',  'body_news.tpl',  '_blank'),
(3, 'QBANR',  'qBanner',  'qBanner module storage', '0',  '0',  '0',  '1',  0,  0,  '0',  0,  '0',  '0',  '', '0',  '', 't',  '1',  'body_default.tpl', 'page_default.tpl'),
(4, 'SSHOW',  'Slideshow',  'Slideshow module storage', '0',  '0',  '0',  '1',  0,  0,  '0',  0,  '0',  '0',  '', '0',  '', 't',  '1',  'body_default.tpl', 'page_default.tpl');

INSERT INTO `__PREFIX__permalink` (`idx`, `url`, `target_script`, `target_idx`, `target_param`) VALUES
(1,	'welcome.php',	'page.php',	'1',	''),
(2,	'privacy-policy.php',	'page.php',	'2',	''),
(3,	'faqs.php',	'page.php',	'3',	''),
(4,	'about-us.php',	'page.php',	'4',	''),
(5,	'contact-us.php',	'page.php',	'6',	''),
(6,	'terms-and-conditions.php',	'page.php',	'7',	''),
(7,	'powered-by-qengine.php',	'page.php',	'8',	''),
(8,	'news/site-is-now-up-and-ready.php',	'page.php',	'9',	''),
(9,	'general-pages.php',	'page.php',	'1',	'list'),
(10,	'news.php',	'page.php',	'2',	'list'),
(11,	'transaction-success.php',	'page.php',	'5',	''),
(12,	'company-directory.php',	'index.php',	'1',	'dir'),
(13,	'category/default-category.php',	'listing_search.php',	'1',	'cmd=list'),
(14,	'example-listing.php',	'detail.php',	'1',	''),
(15,	'microsoft.php',	'detail.php',	'2',	''),
(16,	'apple.php',	'detail.php',	'3',	''),
(17,	'google.php',	'detail.php',	'4',	''),
(18,	'c97net.php',	'detail.php',	'5',	''),
(19,	'category/another-category.php',	'listing_search.php',	'2',	'cmd=list'),
(20,	'category/yet-another-category.php',	'listing_search.php',	'3',	'cmd=list'),
(21,	'category/and-another.php',	'listing_search.php',	'4',	'cmd=list'),
(22,	'category/deep-category-demo.php',	'listing_search.php',	'5',	'cmd=list'),
(23,	'category/quite-deep.php',	'listing_search.php',	'6',	'cmd=list'),
(24,	'category/very-deep.php',	'listing_search.php',	'7',	'cmd=list'),
(25,	'category/really-deep.php',	'listing_search.php',	'8',	'cmd=list'),
(26,	'contoso-ltd.php',	'detail.php',	'6',	''),
(27,	'car-for-sale.php',	'index.php',	'2',	'dir'),
(28,	'category/audi.php',	'listing_search.php',	'9',	'cmd=list'),
(29,	'category/bmw.php',	'listing_search.php',	'10',	'cmd=list'),
(30,	'category/ferrari.php',	'listing_search.php',	'11',	'cmd=list'),
(31,	'category/honda.php',	'listing_search.php',	'12',	'cmd=list'),
(32,	'category/mercedes-benz.php',	'listing_search.php',	'13',	'cmd=list'),
(33,	'category/mitsubishi.php',	'listing_search.php',	'14',	'cmd=list'),
(34,	'category/porsche.php',	'listing_search.php',	'15',	'cmd=list'),
(35,	'category/toyota.php',	'listing_search.php',	'16',	'cmd=list'),
(36,	'audi-q3-1-4tfsi-turbo-white-2014.php',	'detail.php',	'7',	''),
(38,	'bmw-320i-luxury-2015-black.php',	'detail.php',	'9',	''),
(39,	'ferrari-458-spider-2012.php',	'detail.php',	'10',	''),
(40,	'honda-civic-type-r-2015.php',	'detail.php',	'11',	''),
(41,  'backlinking-instructions.php', 'page.php', '14', '');

INSERT INTO `__PREFIX__qcomment` (`comment_id`, `mod_id`, `item_id`, `item_title`, `item_url`, `comment_user`, `comment_title`, `comment_body`, `comment_date`, `comment_rate`, `comment_helpful`, `comment_approve`) VALUES
(1,	'listing',	'5',	'C97net',	'__SITE__/detail.php?item_id=5',	'admin',	'Awesome company!',	'Of course....',	'2016-09-27',	5,	'0|0',	'1');

INSERT INTO `__PREFIX__qcomment_set` (`group_id`, `comment_mode`, `comment_approval`, `member_only`, `unique_comment`, `comment_helpful`, `comment_on_comment`, `captcha`, `detail`, `mod_id`, `notes`) VALUES
(1,	'2',	'0',	'0',	'0',	'0',	'1',	'0',	'0',	'conc',	'Comments on comments'),
(3,	'2',	'1',	'0',	'0',	'0',	'1',	'0',	'1',	'pagecomment',	'Page Comment'),
(4,	'3',	'1',	'1',	'1',	'1',	'1',	'1',	'1',	'listing',	'Listing Reviews');