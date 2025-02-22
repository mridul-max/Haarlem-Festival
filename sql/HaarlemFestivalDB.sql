
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `hashPassword` varchar(256) NOT NULL,
  `firstName` varchar(256) NOT NULL,
  `lastName` varchar(256) NOT NULL,
  `userType` int(1) NOT NULL,
  `registrationDate` date DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `addressId` int(11) NOT NULL AUTO_INCREMENT,
  `streetName` varchar(100) NOT NULL,
  `houseNumber` varchar(10) NOT NULL,
  `postalCode` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  PRIMARY KEY (`addressId`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `addresses` WRITE;
INSERT INTO `addresses` VALUES (5,'Spelderholt','213 ','1025BM','Amsterdam','Netherlands');
INSERT INTO `addresses` VALUES (11,'Poelenburg','254','1504NL','Zaandam','Netherlands');
INSERT INTO `addresses` VALUES (13,'Zijlsingel','2','2013DN','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (14,'Nieuwe Kerksplein','22','2011ZT','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (16,'Grote Markt','16','2011RD','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (17,'Grote Markt','16','2011RD','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (18,'Grote Houtstraat','142','2011SV','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (19,'Gedempte Voldersgracht','2','2011 WD','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (20,'Begijnhof','28','2011HE','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (21,'Papentorenvest','1','2011AV','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (22,'','','2011BZ','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (23,'','','2011BZ','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (24,'','','2011BZ','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (25,'Gedempte Herensingel','58','2032NT','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (26,'Wijde Appelaarsteeg','11','2011HB','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (27,'Spelderholt','213','1025BM','Amsterdam','Netherlands');
INSERT INTO `addresses` VALUES (28,'Zijlsingel','2','2013DN','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (29,'Grote Markt','17','2011RC','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (30,'Zijlsingel','2','2013DN','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (31,'de Blankenstraat','3','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (32,'Street','1 ','1234AB','City','Netherlands');
INSERT INTO `addresses` VALUES (33,'Bijdorplaan','15','2015CE','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (34,'Kromme Elleboogsteeg','20','2011TS','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (35,'Hoge Duin en Daalseweg','2','2061 AG','Bloemendaal','Netherlands');
INSERT INTO `addresses` VALUES (36,'Gedempte Voldersgracht','2','2011WD','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (37,'Minckelersweg','2','2031EM','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (38,'Smedestraat','31','2011RE','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (39,'Grote Markt','8','2011RD','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (40,'Kusocinskiego','4 A','26-600','Radom','Poland');
INSERT INTO `addresses` VALUES (41,'de Blankenstraat','3','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (42,'','','','','');
INSERT INTO `addresses` VALUES (43,'de Blankenstraat','3','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (44,'De Blankenstraat','3 ','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (45,'de Blankenstraat','3','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (47,'Klokhuisplein','9','2011 HK','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (48,'Spaarne','96','2011 CL','Haarlem','Netherlands');
INSERT INTO `addresses` VALUES (49,'De Blankenstraat','3 ','2377VB','Oude Wetering','Netherlands');
INSERT INTO `addresses` VALUES (50,'de Blankenstraat','3 ','2377VB','Oude Wetering','Netherlands');
UNLOCK TABLES;

DROP TABLE IF EXISTS `apikeys`;
CREATE TABLE `apikeys` (
  `apiKeyId` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`apiKeyId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `apikeys` WRITE;
INSERT INTO `apikeys` VALUES (1,'af55cW1am9DlsOvofE4yXaBQtCTEXNDR','Api Key 1');
INSERT INTO `apikeys` VALUES (5,'DYBJ79OKHtx2ieJdEpH9zamT1SqNfnxC','API Key 2');
UNLOCK TABLES;

DROP TABLE IF EXISTS `artistkinds`;
CREATE TABLE `artistkinds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

LOCK TABLES `artistkinds` WRITE;
INSERT INTO `artistkinds` VALUES (1,'Jazz');
UNLOCK TABLES;


DROP TABLE IF EXISTS `artists`;
CREATE TABLE `artists` (
  `artistId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `recentAlbums` varchar(255) DEFAULT NULL,
  `genres` varchar(255) DEFAULT NULL,
  `country` varchar(127) DEFAULT NULL,
  `homepageUrl` varchar(2048) DEFAULT NULL,
  `facebookUrl` varchar(2048) DEFAULT NULL,
  `twitterUrl` varchar(2048) DEFAULT NULL,
  `instagramUrl` varchar(2048) DEFAULT NULL,
  `spotifyUrl` varchar(2048) DEFAULT NULL,
  `artistKindId` int(11) NOT NULL,
  PRIMARY KEY (`artistId`),
  KEY `FK_ArtistToArtistKindId` (`artistKindId`),
  CONSTRAINT `FK_ArtistToArtistKindId` FOREIGN KEY (`artistKindId`) REFERENCES `artistkinds` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `artists` WRITE;
/*!40000 ALTER TABLE `artists` DISABLE KEYS */;
INSERT INTO `artists` VALUES (1,'Gumbo Kings','The Gumbo Kings are a five-member band from the Netherlands known for their blend of soul, rhythm and blues, and swamp rock. &lt;br&gt;&lt;br&gt;They have released a self-titled debut album and are known for incorporating elements of 70s soul-funk, 80s drum computers, and synth soundscapes into their music. The band tours frequently and has gained a loyal fanbase and critical acclaim.','I wonder, Gumbo Kings, Changes Somehow','Soul, Rhythm &amp; Blues','The Netherlands','','https://www.facebook.com/thegumbokings','','https://www.instagram.com/gumbokings','https://open.spotify.com/artist/1j0vpirnPJTpjYHRAInw3n',1);
INSERT INTO `artists` VALUES (7,'Ntjam Rosie','Ntjam Rosie is a Cameroonian-Dutch singer and songwriter known for blending jazz, funk, and Afrobeat in her music.&lt;br&gt;&lt;br&gt;She has released multiple critically acclaimed albums and tours internationally, performing at various venues and festivals. Rosie promotes social justice and equality through her music and message.','Home Cooking, Family &amp; Friends, Breaking Cycles','Jazz, Soul','The Netherlands','','https://www.facebook.com/ntjamrosiemusic/','https://twitter.com/NtjamRosie','https://www.instagram.com/ntjamrosie/','https://open.spotify.com/artist/44XhJ4fcKrMzrVr6WpF69R',1);
INSERT INTO `artists` VALUES (8,'Gare du Nord','Gare du Nord is a Dutch band that was formed in 1998 and plays a mix of jazz, funk, soul, and pop. &lt;br&gt;&lt;br&gt;â€¨â€¨The band&#039;s lineup consists of vocalist Martijn ten Velden, saxophonist Ben Hazleton, keyboardist Jan van Duikeren, bassist Bart Wirtz, drummer Paul Willemen, and percussionist Tijs Klaverstijn. Gare du Nord has released several successful albums and toured extensively, performing at venues and festivals worldwide.','Play, Sex &#039;N&#039; Jazz, Rende Vous','Jazz, Funk, Soul, Pop','The Netherlands, Belgium','','https://www.facebook.com/garedunord','','','https://open.spotify.com/artist/0fvpn2k7FymYHxEx5U5FpP?autoplay=true',1);
INSERT INTO `artists` VALUES (9,'The Nordanians','When Oene van Geel viola, Mark Tuinstra guitar and Niti Ranjan Biswas tabla virtuoso played together for the first time there where immediately fireworks, roaring u-turns and cinematic tearjerkers. Then they started writing songs together based on traditional ragas, smashing funk and delicate chamber music.&lt;br&gt;&lt;br&gt;This gave them a great new impulse on stage for even more interaction and improvisation and made them build a rocking live reputation. They love to play with the three of them but they also play with special guests from around the globe such as Fraser Fifield whistle / pipes, Jorg Brinkmann cello, Maarten Ornstein bass clarinet, Theo Loevendie sop sax, Druba Ghosh sarangi, Bruno Ferro Xavier da Silva bass guitar, Barbara Schilstra (vocals), Bao Sisoko (kora) and Benedicte Maurseth hardanger fiddle.','Tabla Rasa','Jazz','The Netherlands','','https://www.facebook.com/Nordanians/','','','https://open.spotify.com/artist/2euGZQXIbIpW8OlRrdVZhf',1);
INSERT INTO `artists` VALUES (12,'Evolve','','','','','','','','','',1);
INSERT INTO `artists` VALUES (13,'Wicked Jazz Sounds','Wicked Jazz Sounds is an Amsterdam-based event organisation that has become a platform for music lovers. The two founders Phil Horneman and Manne van der Zee started a club night in 2002 where DJs and live musicians improvised together on a dancefloor-focused mix of funk, soul, hip-hop, house, jazz and more.','','Funk, Soul, Hip Hop, House, Jazz','The Netherlands','','https://www.facebook.com/wickedjazzsounds/','','https://www.instagram.com/wickedjazz/','https://open.spotify.com/artist/0JhIXbP3aPERorDqoKu3BF',1);
INSERT INTO `artists` VALUES (14,'Tom Thomson Assemble','','','','','','','','','',1);
INSERT INTO `artists` VALUES (15,'Jonna Fraser','Jonna Fraser, in full Jonathan Jeffrey Grando, is a Dutch rapper and singer of Surinamese descent. He has a broad nederhop style that ranges from gangsta rap to sultry soul. He released several albums, including Goed teken which managed to reach the eleventh position of the album chart. The single Do or die, which he recorded with rap formation Broederliefde, reached number 10 in 2016. He is also part of the rap collective New Wave, which won the 2015 Pop Award. ','Championships, Champagne Rain, Calma','Pop, Nederhop, Rap','The Netherlands','','https://www.facebook.com/jonnafraser','https://twitter.com/jonnafraser','https://www.instagram.com/jonnafraser','https://open.spotify.com/artist/5adKMaYrGOMyOfnbiLPuHg',1);
INSERT INTO `artists` VALUES (16,'Fox &amp; The Mayors','','','','','','','','','',1);
INSERT INTO `artists` VALUES (17,'Uncle Sue','Uncle Sue is a seven-piece Haarlem Funk and Soul Band with its own story, soul diva and swinging horn section. &lt;br&gt;Quirky repertoire, from their own studio and slightly less obvious gems by our musical heroes. A sound that harks back to the 60s and 70s. That&#039;s where Uncle Sue feels at home. This is reflected in their own retro look. Tight in suits with classy energetic singer. Think Sharon Jones &amp; the Dap Kings, Bamboos, Slim Moore, James Brown, Amy Winehouse, Beck, Trombone Shorty, Otis Redding et al.','New Dimension Of Life','Funk, Soul','The Netherlands','','https://www.facebook.com/unclesue/','','','https://open.spotify.com/artist/61Oa2dakzgX5019WmmsRg8',1);
INSERT INTO `artists` VALUES (18,'Kris Allen','Kris Allen is a singer-songwriter and musician known for his soulful voice and heartfelt lyrics. Born in Jacksonville, Arkansas, Kris rose to national fame as the winner of the eighth season of American Idol in 2009. Since then, he has released several successful albums, including his self-titled debut album, &quot;Kris Allen,&quot; and his latest release, &quot;Letting You In.&quot;&lt;br&gt;&lt;br&gt;Kris has toured extensively throughout the United States and internationally, performing at festivals, theaters, and arenas. His live shows are a dynamic mix of acoustic guitar-driven pop-rock, bluesy ballads, and soulful R&amp;B. Kris is known for his ability to connect with his audience, delivering powerful and emotional performances that leave a lasting impression.','10, Letting You In, Horizons','Pop rock, Alternative rock, Soul','United States of America','','https://www.facebook.com/KrisAllen','','','https://open.spotify.com/artist/2zwHaEmXxX6DTv4i8ajNCM',1);
INSERT INTO `artists` VALUES (19,'Myles Sanko','Myles Sanko is a British soul singer and songwriter known for his smooth and soulful voice, captivating lyrics, and dynamic live performances. Born in Ghana and raised in the UK, Myles draws inspiration from a wide range of musical genres, including jazz, funk, and soul.&lt;br&gt;&lt;br&gt;With his distinctive sound and engaging stage presence, Myles has gained a loyal following around the world, touring extensively across Europe, Asia, and the Americas. His live shows are a mesmerizing fusion of soulful melodies, powerful vocals, and tight rhythms, leaving audiences spellbound and craving more.','Forever Dreaming, Born in Black &amp; White','Jazz, Funk, Soul','British','','https://www.facebook.com/mylessankofanpage','','https://www.instagram.com/mylessanko/','https://open.spotify.com/artist/0EeY17gAdOJIBjNrpi6q1G?autoplay=true',1);
INSERT INTO `artists` VALUES (20,'Ruis Soundsystem','','','','','','','','','',1);
INSERT INTO `artists` VALUES (21,'The Family XL','','','','','','','','','',1);
INSERT INTO `artists` VALUES (22,'Rilan &amp; The Bombardiers','Rilan &amp; the Bombardiers is characterised by its eclectic style of pop, funk, rap, rythm-and-blues. The energetic live show and frontman Rilan&#039;s charismatic and unique performance make sure you won&#039;t forget a gig any time soon.','Walking On Fire, Drowning','Soul, Rock, Funk','The Netherlands','','https://www.facebook.com/RilanandtheBombardiers/?locale=nl_NL','','','https://open.spotify.com/artist/1yawxcvEJTTtsz2aX3yruE',1);
INSERT INTO `artists` VALUES (23,'Soul Six','','','','','','','','','',1);
INSERT INTO `artists` VALUES (24,'Han Bennink','Han Bennink is a Dutch jazz drummer and percussionist known for his dynamic and innovative style, fearless improvisation, and irreverent humor. Born in Zaandam, Netherlands, Han began his music career in the 1960s, performing with jazz legends like Eric Dolphy and Dexter Gordon.&lt;br&gt;&lt;br&gt;With his unique approach to drumming and percussion, Han has pushed the boundaries of jazz, experimenting with a wide range of sounds and techniques, from traditional swing to free jazz and avant-garde. He is known for his ability to blend different rhythms and styles, creating a dynamic and unpredictable musical experience.','Home Safely, Icarus, Welcome Back','Jazz','The Netherlands','','','https://twitter.com/han_bennink','https://www.instagram.com/hanbennink/','https://open.spotify.com/artist/0tmLlnSIrAb8NZajutucCC',1);
INSERT INTO `artists` VALUES (25,'Lilth Merlot','Lilith Merlot is known for her warm and deep voice with a timeless feel. Growing up in a family of classically trained professional musicians, Lilith was enchanted by the beauty of harmony and melody from a very young age.','Easier to Fight, Speak Your Heart','R&amp;B, Soul, Jazz, Pop','The Netherlands','','https://www.facebook.com/lilithmerlot/','','https://www.instagram.com/lilithmerlot/','https://open.spotify.com/artist/1aj2btWZXYFQP5KhTKGO0s',1);
UNLOCK TABLES;

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `imageId` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(128) NOT NULL,
  `alt` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`imageId`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `images` WRITE;

INSERT INTO `images` VALUES (1,'/img/jpg/BACKGROUND.jpg','Visit Haarlem');
INSERT INTO `images` VALUES (2,'/img/jpg/background2.jpg','Visit Haarlem');
INSERT INTO `images` VALUES (4,'/img/png/image_5.png','The Festival');
INSERT INTO `images` VALUES (6,'/img/jpg/Jazz.jpg','Haarlem Jazz');
INSERT INTO `images` VALUES (7,'/img/jpg/History.jpg','A Stroll Through Haarlem');
INSERT INTO `images` VALUES (9,'/img/jpg/teylers.jpg','The Teyler Mystery');
INSERT INTO `images` VALUES (10,'/img/jpg/NtjamRosie.jpg','Ntjam Rosie');
INSERT INTO `images` VALUES (11,'/img/jpg/763.jpg','Ntjam Rosie');
INSERT INTO `images` VALUES (12,'/img/jpg/52958_Ntjam_Rosie_21293.jpg','Ntjam Rosie');
INSERT INTO `images` VALUES (13,'/img/jpg/52958_Ntjam_Rosie_20954.jpg','Ntjam Rosie');
INSERT INTO `images` VALUES (14,'/img/jpg/GumboKings.jpg','Gumbo Kings');
INSERT INTO `images` VALUES (15,'/img/jpg/Gumbo-Kings.jpg','Gumbo Kings');
INSERT INTO `images` VALUES (16,'/img/jpg/Nordanians-1.jpg','The Nordanians');
INSERT INTO `images` VALUES (17,'/img/jpg/HaarlemGroteMarkt1.JPG','St. Bavo Church');
INSERT INTO `images` VALUES (18,'/img/jpg/brouwerij-restaurant-jopenkerk-haarlem-jopenbier_4082379069.jpg','Jopenkerk Beer');
INSERT INTO `images` VALUES (19,'/img/jpg/Frame_21.jpg','Grote Markt');
INSERT INTO `images` VALUES (20,'/img/jpg/Frame_212.jpg','De Hallen');
INSERT INTO `images` VALUES (21,'/img/jpg/Frame_307.jpg','Jopenkerk');
INSERT INTO `images` VALUES (22,'/img/jpg/Frame_21(1).jpg','Proveniershof');
INSERT INTO `images` VALUES (23,'/img/jpg/Frame_21(2).jpg','Waalse Kerk');
INSERT INTO `images` VALUES (24,'/img/jpg/Frame_21(3).jpg','Molen de Adriaan');
INSERT INTO `images` VALUES (25,'/img/jpg/Frame_21(4).jpg','Amsterdamse Poort');
INSERT INTO `images` VALUES (26,'/img/jpg/Frame_21(5).jpg','Hof van Bakenes');
INSERT INTO `images` VALUES (28,'/img/jpg/Image.jpg','Haarlem Jazz');
INSERT INTO `images` VALUES (29,'/img/jpg/Image(1).jpg','Stadsschouwburg &amp; Philharmonie Haarlem');
INSERT INTO `images` VALUES (30,'/img/jpg/Image(2).jpg','Patronaat');
INSERT INTO `images` VALUES (31,'/img/jpg/bottom.jpg','Bottom Music &amp; Dance');
INSERT INTO `images` VALUES (32,'/img/jpg/history(1).jpg','History');
INSERT INTO `images` VALUES (34,'/img/jpg/history(2).jpg','Art');
INSERT INTO `images` VALUES (35,'/img/jpg/kids.jpg','Kids');
INSERT INTO `images` VALUES (36,'/img/jpg/imgcounter.jpg','Image Counter');
INSERT INTO `images` VALUES (37,'/img/jpg/Gare_du_Nord_1082.jpg','Gare du Nord');
INSERT INTO `images` VALUES (38,'/img/jpg/GdN_presspic_Staand-scaled-e1649839659934.jpg','Gare du Nord');
INSERT INTO `images` VALUES (39,'/img/jpg/Gare_Du_Nord.jpg','Gare du Nord');
INSERT INTO `images` VALUES (40,'/img/jpg/gare.jpg','Gare du Nord');
INSERT INTO `images` VALUES (41,'/img/jpg/Nordanians-1(1).jpg','The Nordanians');
INSERT INTO `images` VALUES (42,'/img/jpg/33036422_2199605456721417_3092494307022602240_n.jpg','The Nordanians');
INSERT INTO `images` VALUES (43,'/img/jpg/Nordanians-2.jpg','The Nordanians');
INSERT INTO `images` VALUES (44,'/img/jpg/wickedJazz.jpg','Wicked Jazz Sounds');
INSERT INTO `images` VALUES (45,'/img/jpg/JonnaFraser.jpg','Jonna Fraser');
INSERT INTO `images` VALUES (46,'/img/jpg/maxresdefault-1.jpg','Jonna Fraser');
INSERT INTO `images` VALUES (47,'/img/jpg/Wicked-jazz-sounds-podium-1024x675.jpg','Wicked Jazz Sounds');
INSERT INTO `images` VALUES (49,'/img/webp/Uncle-Sue-bandfoto-10.webp','Uncle Sue');
INSERT INTO `images` VALUES (50,'/img/webp/Uncle-Sue-podium-22.webp','Uncle Sue');
INSERT INTO `images` VALUES (51,'/img/webp/Uncle-Sue-podium-16.webp','Uncle Sue');
INSERT INTO `images` VALUES (52,'/img/png/BCF8428E-5A1E-4B5F-AC44-F085A629C731.png','Kris Allen');
INSERT INTO `images` VALUES (53,'/img/jpg/myles-sanko-1140x642.jpg','Myles Sanko');
INSERT INTO `images` VALUES (54,'/img/jpg/cb65da9a8be1899ff923b166c9ad9dd4.jpg','Myles Sanko');
INSERT INTO `images` VALUES (55,'/img/jpg/15_Myles-Sanko.jpg','Myles Sanko');
INSERT INTO `images` VALUES (56,'/img/jpg/22254979_2030325053855095_5034947054993221983_o.jpg','The Family XL');
INSERT INTO `images` VALUES (57,'/img/jpg/22221507_2030324933855107_2469730967042660850_n.jpg','The Family Xl');
INSERT INTO `images` VALUES (58,'/img/jpg/b33fc364-46df-47f9-88fe-9986b11c54d0_thumb1440.jpg','Rilan &amp; The Bombardiers');
INSERT INTO `images` VALUES (59,'/img/jpg/data36752617-547464.jpg','Rilan &amp; The Bombardiers');
INSERT INTO `images` VALUES (60,'/img/jpg/69650218_3211291552244922_6963147898221494272_n.jpg','Soul Six');
INSERT INTO `images` VALUES (61,'/img/jpg/72074381_3326183484089061_6188742728994521088_n.jpg','Soul Six');
INSERT INTO `images` VALUES (62,'/img/jpg/72421516_3326183677422375_6121295065089310720_n.jpg','Soul Six');
INSERT INTO `images` VALUES (63,'/img/jpg/1280px-Anderson,_Bennink,_Glerum,_van_Kemenade_02.jpg','Han Bennink');
INSERT INTO `images` VALUES (64,'/img/jpg/Han_Bennink,_Canada_2015_DSC_1125.jpg','Han Bennink');
INSERT INTO `images` VALUES (65,'/img/jpg/cf31f2_1209ac9c542e4d81938e380d2a0e2273~mv2.jpg','Lilth Merlot');
INSERT INTO `images` VALUES (66,'/img/jpg/copyrights-RONA-LANE-50.jpg','Lilth Merlot');
INSERT INTO `images` VALUES (67,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.22.57_(1).jpg','');
INSERT INTO `images` VALUES (68,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.22.57.jpg','');
INSERT INTO `images` VALUES (69,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24.jpg','');
INSERT INTO `images` VALUES (70,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24_(1).jpg','');
INSERT INTO `images` VALUES (71,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24_(2).jpg','');
INSERT INTO `images` VALUES (72,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.45.15_(1).jpg','');
INSERT INTO `images` VALUES (73,'/img/jpg/WhatsApp_Image_2025-06-21_at_20.45.15.jpg','');
INSERT INTO `images` VALUES (74,'/img/jpg/nzhvervoermuseumbussentrams_vervoermuseumtramsbussenhaarlem_3_350_235.jpg','');
INSERT INTO `images` VALUES (75,'/img/jpg/Verwey-Museum-Haarlem-nieuw-logo-en-banieren-LR.jpg','');
UNLOCK TABLES;

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `href` varchar(128) NOT NULL,
  `location` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `href` (`href`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

LOCK TABLES `pages` WRITE;
INSERT INTO `pages` VALUES (1,'Haarlem Festival','/','/views/home/index.php');
INSERT INTO `pages` VALUES (4,'Haarlem Festival','/festival','/views/festival/index.php');
INSERT INTO `pages` VALUES (7,'History','/history','/views/home/history.php');
INSERT INTO `pages` VALUES (11,'Haarlem Jazz','/festival/jazz','/views/festival/jazz-and-more.php');
INSERT INTO `pages` VALUES (15,'History','/festival/history-stroll-2','');
UNLOCK TABLES;

DROP TABLE IF EXISTS `bannerimages`;
CREATE TABLE `bannerimages` (
  `imageId` int(11) NOT NULL,
  `pageId` int(11) NOT NULL,
  PRIMARY KEY (`imageId`,`pageId`),
  UNIQUE KEY `imageId` (`imageId`,`pageId`),
  KEY `FK_Page_Id_Banner` (`pageId`),
  CONSTRAINT `FK_ImageId` FOREIGN KEY (`imageId`) REFERENCES `images` (`imageId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Page_Id_Banner` FOREIGN KEY (`pageId`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `bannerimages` WRITE;
INSERT INTO `bannerimages` VALUES (1,1);
INSERT INTO `bannerimages` VALUES (2,1);
INSERT INTO `bannerimages` VALUES (4,4);
INSERT INTO `bannerimages` VALUES (2,7);
INSERT INTO `bannerimages` VALUES (6,11);
INSERT INTO `bannerimages` VALUES (7,15);
UNLOCK TABLES;


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `userId` int(11) NOT NULL,
  `dateOfBirth` datetime NOT NULL,
  `phoneNumber` varchar(32) NOT NULL,
  `addressId` int(11) NOT NULL,
  PRIMARY KEY (`userId`),
  KEY `userId` (`userId`),
  KEY `customers_FK` (`addressId`),
  CONSTRAINT `customers_FK` FOREIGN KEY (`addressId`) REFERENCES `addresses` (`addressId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `festivaleventtypes`;
CREATE TABLE `festivaleventtypes` (
  `eventTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `VAT` decimal(3,2) NOT NULL,
  PRIMARY KEY (`eventTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `festivaleventtypes` WRITE;
INSERT INTO `festivaleventtypes` VALUES (1,'Haarlem Jazz',0.09);
INSERT INTO `festivaleventtypes` VALUES (3,'A Historical Haarlem',0.21);
UNLOCK TABLES;

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `festivalEventType` int(11) DEFAULT NULL,
  `availableTickets` int(11) DEFAULT NULL,
  PRIMARY KEY (`eventId`),
  KEY `events_FK` (`festivalEventType`),
  CONSTRAINT `events_FK` FOREIGN KEY (`festivalEventType`) REFERENCES `festivaleventtypes` (`eventTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Gumbo Kings','2025-07-27 18:00:00','2025-07-27 19:00:00',1,300);
INSERT INTO `events` VALUES (2,'A Historical Haarlem','2025-07-27 10:00:00','2025-07-27 12:30:00',3,12);
INSERT INTO `events` VALUES (4,'A Historical Haarlem','2025-07-27 13:00:00','2025-07-27 15:30:00',3,12);
INSERT INTO `events` VALUES (7,'Ntjam Rosie','2025-07-27 21:00:00','2025-07-27 22:00:00',1,300);
INSERT INTO `events` VALUES (8,'Gumbo Kings','2025-07-30 19:00:00','2025-07-30 20:00:00',1,10000);
INSERT INTO `events` VALUES (14,'Jazz Pass Thursday','2025-07-27 02:00:00','2025-07-27 02:00:00',1,0);
INSERT INTO `events` VALUES (15,'Jazz Pass Friday','2025-07-28 02:00:00','2025-07-28 02:00:00',1,0);
INSERT INTO `events` VALUES (16,'Jazz Pass Saturday','2025-07-29 02:00:00','2025-07-29 02:00:00',1,0);
INSERT INTO `events` VALUES (17,'Jazz Pass Sunday','2025-07-30 02:00:00','2025-07-30 02:00:00',1,0);
INSERT INTO `events` VALUES (18,'Jazz Pass All Days','2025-07-27 02:00:00','2025-07-27 02:00:00',1,0);
INSERT INTO `events` VALUES (19,'Gare du Nord','2025-07-29 18:00:00','2025-07-29 19:00:00',1,300);
INSERT INTO `events` VALUES (20,'Gare du Nord','2025-07-30 20:00:00','2025-07-30 21:00:00',1,10000);
INSERT INTO `events` VALUES (21,'The Nordanians','2025-07-29 19:30:00','2025-07-29 20:30:00',1,150);
INSERT INTO `events` VALUES (22,'The Nordanians','2025-07-30 18:00:00','2025-07-30 19:00:00',1,10000);
INSERT INTO `events` VALUES (23,'Evolve','2025-07-27 19:30:00','2025-07-27 20:30:00',1,300);
INSERT INTO `events` VALUES (24,'Evolve','2025-07-30 17:00:00','2025-07-30 18:00:00',1,10000);
INSERT INTO `events` VALUES (26,'Wicked Jazz Sounds','2025-07-30 16:00:00','2025-07-30 17:00:00',1,10000);
INSERT INTO `events` VALUES (27,'Tom Thomson Assemble','2025-07-27 19:30:00','2025-07-27 20:30:00',1,200);
INSERT INTO `events` VALUES (28,'Jonna Fraser','2025-07-27 21:00:00','2025-07-27 22:00:00',1,200);
INSERT INTO `events` VALUES (29,'Fox &amp; The Mayors','2025-07-28 18:00:00','2025-07-28 19:00:00',1,300);
INSERT INTO `events` VALUES (30,'Uncle Sue','2025-07-28 19:30:00','2025-07-28 20:30:00',1,300);
INSERT INTO `events` VALUES (31,'Kris Allen','2025-07-28 21:00:00','2025-07-28 22:00:00',1,300);
INSERT INTO `events` VALUES (32,'Myles Sanko','2025-07-28 18:00:00','2025-07-28 19:00:00',1,200);
INSERT INTO `events` VALUES (33,'Ruis Soundsystem','2025-07-28 19:30:00','2025-07-28 20:30:00',1,200);
INSERT INTO `events` VALUES (34,'Ruis Soundsystem','2025-07-30 15:00:00','2025-07-30 16:00:00',1,10000);
INSERT INTO `events` VALUES (35,'The Family XL','2025-07-28 21:00:00','2025-07-28 22:00:00',1,200);
INSERT INTO `events` VALUES (36,'Rilan &amp; The Bombardiers','2025-07-29 19:30:00','2025-07-29 20:30:00',1,300);
INSERT INTO `events` VALUES (37,'Soul Six','2025-07-29 21:00:00','2025-07-29 22:00:00',1,300);
INSERT INTO `events` VALUES (38,'Han Bennink','2025-07-29 18:00:00','2025-07-29 19:00:00',1,150);
INSERT INTO `events` VALUES (39,'Lilth Merlot','2025-07-29 21:00:00','2025-07-29 22:00:00',1,150);
INSERT INTO `events` VALUES (42,'Wicked Jazz Sounds','2025-07-27 18:00:00','2025-07-27 19:00:00',1,200);
INSERT INTO `events` VALUES (66,'A Historical Haarlem','2025-07-27 10:00:00','2025-07-27 12:30:00',3,12);
INSERT INTO `events` VALUES (68,'A Historical Haarlem','2025-07-27 10:00:00','2025-07-27 12:30:00',3,12);
INSERT INTO `events` VALUES (69,'A Historical Haarlem','2025-07-27 10:00:00','2025-07-27 12:30:00',3,12);
INSERT INTO `events` VALUES (71,'A Historical Haarlem','2025-07-27 13:00:00','2025-07-27 15:30:00',3,12);
INSERT INTO `events` VALUES (72,'A Historical Haarlem','2025-07-27 13:00:00','2025-07-27 15:30:00',3,12);
INSERT INTO `events` VALUES (74,'A Historical Haarlem','2025-07-27 16:00:00','2025-07-27 18:30:00',3,12);
INSERT INTO `events` VALUES (75,'A Historical Haarlem','2025-07-27 16:00:00','2025-07-27 18:30:00',3,12);
INSERT INTO `events` VALUES (76,'A Historical Haarlem','2025-07-28 10:00:00','2025-07-28 12:30:00',3,12);
INSERT INTO `events` VALUES (77,'A Historical Haarlem','2025-07-28 10:00:00','2025-07-28 12:30:00',3,12);
INSERT INTO `events` VALUES (78,'A Historical Haarlem','2025-07-28 10:00:00','2025-07-28 12:30:00',3,12);
INSERT INTO `events` VALUES (79,'A Historical Haarlem','2025-07-28 10:00:00','2025-07-28 12:30:00',3,12);
INSERT INTO `events` VALUES (80,'A Historical Haarlem','2025-07-28 13:00:00','2025-07-28 15:30:00',3,12);
INSERT INTO `events` VALUES (81,'A Historical Haarlem','2025-07-28 13:00:00','2025-07-28 15:30:00',3,12);
INSERT INTO `events` VALUES (82,'A Historical Haarlem','2025-07-28 13:00:00','2025-07-28 15:30:00',3,12);
INSERT INTO `events` VALUES (83,'A Historical Haarlem','2025-07-28 16:00:00','2025-07-28 18:30:00',3,12);
INSERT INTO `events` VALUES (84,'A Historical Haarlem','2025-07-28 16:00:00','2025-07-28 18:30:00',3,12);
INSERT INTO `events` VALUES (85,'A Historical Haarlem','2025-07-28 16:00:00','2025-07-28 18:30:00',3,12);
INSERT INTO `events` VALUES (86,'A Historical Haarlem','2025-07-28 16:00:00','2025-07-28 18:30:00',3,12);
INSERT INTO `events` VALUES (87,'A Historical Haarlem','2025-07-29 10:00:00','2025-07-29 12:30:00',3,12);
INSERT INTO `events` VALUES (88,'A Historical Haarlem','2025-07-29 10:00:00','2025-07-29 12:30:00',3,12);
INSERT INTO `events` VALUES (89,'A Historical Haarlem','2025-07-29 10:00:00','2025-07-29 12:30:00',3,12);
INSERT INTO `events` VALUES (91,'A Historical Haarlem','2025-07-29 13:00:00','2025-07-29 15:30:00',3,12);
INSERT INTO `events` VALUES (92,'A Historical Haarlem','2025-07-29 13:00:00','2025-07-29 15:30:00',3,12);
INSERT INTO `events` VALUES (94,'A Historical Haarlem','2025-07-29 13:00:00','2025-07-29 15:30:00',3,12);
INSERT INTO `events` VALUES (95,'A Historical Haarlem','2025-07-29 16:00:00','2025-07-29 18:30:00',3,12);
INSERT INTO `events` VALUES (96,'A Historical Haarlem','2025-07-29 16:00:00','2025-07-29 18:30:00',3,12);
INSERT INTO `events` VALUES (97,'A Historical Haarlem','2025-07-29 16:00:00','2025-07-29 18:30:00',3,12);
INSERT INTO `events` VALUES (98,'A Historical Haarlem','2025-07-30 10:00:00','2025-07-30 12:30:00',3,12);
INSERT INTO `events` VALUES (99,'A Historical Haarlem','2025-07-30 10:00:00','2025-07-30 12:30:00',3,12);
INSERT INTO `events` VALUES (101,'A Historical Haarlem','2025-07-30 10:00:00','2025-07-30 12:30:00',3,12);
INSERT INTO `events` VALUES (102,'A Historical Haarlem','2025-07-30 13:00:00','2025-07-30 15:30:00',3,12);
INSERT INTO `events` VALUES (103,'A Historical Haarlem','2025-07-30 13:00:00','2025-07-30 15:30:00',3,12);
INSERT INTO `events` VALUES (104,'A Historical Haarlem','2025-07-30 13:00:00','2025-07-30 15:30:00',3,12);
INSERT INTO `events` VALUES (105,'A Historical Haarlem','2025-07-30 13:00:00','2025-07-30 15:30:00',3,12);
INSERT INTO `events` VALUES (106,'A Historical Haarlem','2025-07-30 16:00:00','2025-07-30 18:30:00',3,12);
INSERT INTO `events` VALUES (107,'A Historical Haarlem','2025-07-30 16:00:00','2025-07-30 18:30:00',3,12);
INSERT INTO `events` VALUES (108,'A Historical Haarlem','2025-07-30 16:00:00','2025-07-30 18:30:00',3,12);
INSERT INTO `events` VALUES (109,'A Historical Haarlem','2025-07-30 16:00:00','2025-07-30 18:30:00',3,12);
UNLOCK TABLES;


DROP TABLE IF EXISTS `guides`;
CREATE TABLE `guides` (
  `guideId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`guideId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `guides` WRITE;
INSERT INTO `guides` VALUES (1,'Susan','Can','English',NULL);
INSERT INTO `guides` VALUES (2,'Annet','Marry','Dutch',NULL);
INSERT INTO `guides` VALUES (3,'Kim','Huang','Chinese',NULL);
UNLOCK TABLES;

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `locationId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `addressId` int(11) NOT NULL,
  `locationType` int(11) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `lon` decimal(8,5) NOT NULL,
  `lat` decimal(7,5) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`locationId`),
  KEY `FK_LocationToAddressId` (`addressId`),
  CONSTRAINT `FK_LocationToAddressId` FOREIGN KEY (`addressId`) REFERENCES `addresses` (`addressId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `locations` WRITE;
INSERT INTO `locations` VALUES (1,'Patronaat (Main Hall)',13,1,300,4.62871,52.38300,NULL);
INSERT INTO `locations` VALUES (2,'St. Bavo Church',14,3,0,4.62919,52.37719,'The Sint Bavokerk is the largest church in Haarlem. The St Bavo Church is also called the Grote Kerk and is popularly referred to as the old baaf.  The St Bavo church is already mentioned in documents from 1245. \nSince 1245, the church has expanded to its current size with seven bells and a beautiful tower. To this day, the St Bavo Church is the highest building in Haarlem.');
INSERT INTO `locations` VALUES (3,'Grote Markt',16,3,0,4.63603,52.38113,'The market square features several works of art, including a statue honoring Laurenz Janszoon Coster, who is widely credited with inventing printing in the Netherlands.');
INSERT INTO `locations` VALUES (4,'De Hallen',17,3,0,4.63603,52.38113,'De Hallen is a contemporary art museum hosting exhibitions featuring national and international artists. Exhibitions are held three times a year and focus on current developments in the visual arts.');
INSERT INTO `locations` VALUES (7,'Waalse Kerk',20,3,0,4.63915,52.38254,'The Waalse Kerk is a Walloon church that was built in the 14th century. It has an upper gallery that was originally built for the Beguines who lived on the courtyard that still bears their name. ');
INSERT INTO `locations` VALUES (8,'Molen de Adriaan',21,3,0,4.64264,52.38377,'In 1778, a businessman from Amsterdam purchased an old defense tower in Haarlem and received permission to build a windmill on top of it. The tower was subsequently transformed into a windmill.');
INSERT INTO `locations` VALUES (9,'Amsterdamse Poort',25,3,0,4.64733,52.38053,'The Amsterdamse Poort is a gate located in Haarlem. It is one of the original gates of the city\'s old defensive wall and has been well-preserved over the years. It is a significant part of Haarlem\'s history.');
INSERT INTO `locations` VALUES (10,'Hof van Bakenes',26,3,0,4.63989,52.38146,'The Hofje van Bakenes is located on the Bakenessergracht and has two entrances. The main entrance is located on the Wijde Appelaarsteeg. The courtyard at this location is the oldest one in Haarlem.');
INSERT INTO `locations` VALUES (12,'Patronaat (Second Hall)',28,1,200,4.62871,52.38300,NULL);
INSERT INTO `locations` VALUES (13,'Grote Markt',29,1,10000,4.63647,52.38170,NULL);
INSERT INTO `locations` VALUES (14,'Patronaat (Third Hall)',30,1,150,4.62871,52.38300,NULL);
INSERT INTO `locations` VALUES (17,'Club Stalker',34,4,200,4.63434,52.38223,NULL);
INSERT INTO `locations` VALUES (18,'Caprera Openluchttheater ',35,4,2000,4.60802,52.41121,NULL);
INSERT INTO `locations` VALUES (19,'Jopenkerk',36,4,300,4.62978,52.38104,NULL);
INSERT INTO `locations` VALUES (20,'Lichtfabriek',37,4,1500,4.65174,52.38635,NULL);
INSERT INTO `locations` VALUES (21,'Club Ruis',38,4,200,4.63636,52.38219,NULL);
INSERT INTO `locations` VALUES (22,'XO the Club',39,4,200,4.63521,52.38121,NULL);
INSERT INTO `locations` VALUES (28,'Mr & Mrs',48,2,50,4.63760,52.37868,NULL);
UNLOCK TABLES;

DROP TABLE IF EXISTS `historyevents`;
CREATE TABLE `historyevents` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `guideId` int(11) DEFAULT NULL,
  `locationId` int(11) DEFAULT NULL,
  PRIMARY KEY (`eventId`),
  KEY `historyevents_ibfk_1` (`guideId`),
  KEY `historyevents_ibfk_2` (`locationId`),
  CONSTRAINT `historyevents_ibfk_1` FOREIGN KEY (`guideId`) REFERENCES `guides` (`guideId`) ON UPDATE CASCADE,
  CONSTRAINT `historyevents_ibfk_2` FOREIGN KEY (`locationId`) REFERENCES `locations` (`locationId`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;

LOCK TABLES `historyevents` WRITE;
INSERT INTO `historyevents` VALUES (48,3,2);
INSERT INTO `historyevents` VALUES (49,3,2);
INSERT INTO `historyevents` VALUES (50,3,2);
INSERT INTO `historyevents` VALUES (51,3,2);
INSERT INTO `historyevents` VALUES (52,3,2);
INSERT INTO `historyevents` VALUES (53,3,2);
INSERT INTO `historyevents` VALUES (54,3,2);
INSERT INTO `historyevents` VALUES (55,3,2);
INSERT INTO `historyevents` VALUES (56,3,2);
INSERT INTO `historyevents` VALUES (58,2,2);
INSERT INTO `historyevents` VALUES (60,3,2);
INSERT INTO `historyevents` VALUES (61,3,2);
INSERT INTO `historyevents` VALUES (62,3,2);
INSERT INTO `historyevents` VALUES (63,2,2);
INSERT INTO `historyevents` VALUES (64,3,2);
INSERT INTO `historyevents` VALUES (65,1,2);
INSERT INTO `historyevents` VALUES (66,1,2);
INSERT INTO `historyevents` VALUES (67,2,2);
INSERT INTO `historyevents` VALUES (68,2,2);
INSERT INTO `historyevents` VALUES (69,3,2);
INSERT INTO `historyevents` VALUES (70,3,2);
INSERT INTO `historyevents` VALUES (71,1,2);
INSERT INTO `historyevents` VALUES (72,2,2);
INSERT INTO `historyevents` VALUES (73,2,2);
INSERT INTO `historyevents` VALUES (74,1,2);
INSERT INTO `historyevents` VALUES (75,2,2);
INSERT INTO `historyevents` VALUES (76,1,2);
INSERT INTO `historyevents` VALUES (77,1,2);
INSERT INTO `historyevents` VALUES (78,2,2);
INSERT INTO `historyevents` VALUES (79,3,2);
INSERT INTO `historyevents` VALUES (80,1,2);
INSERT INTO `historyevents` VALUES (81,2,2);
INSERT INTO `historyevents` VALUES (82,3,2);
INSERT INTO `historyevents` VALUES (83,1,2);
INSERT INTO `historyevents` VALUES (84,2,2);
INSERT INTO `historyevents` VALUES (85,3,2);
INSERT INTO `historyevents` VALUES (86,2,2);
INSERT INTO `historyevents` VALUES (87,1,2);
INSERT INTO `historyevents` VALUES (88,2,2);
INSERT INTO `historyevents` VALUES (89,2,2);
INSERT INTO `historyevents` VALUES (90,3,2);
INSERT INTO `historyevents` VALUES (91,1,2);
INSERT INTO `historyevents` VALUES (92,2,2);
INSERT INTO `historyevents` VALUES (93,3,2);
INSERT INTO `historyevents` VALUES (94,3,2);
INSERT INTO `historyevents` VALUES (95,1,2);
INSERT INTO `historyevents` VALUES (96,2,2);
INSERT INTO `historyevents` VALUES (97,3,2);
INSERT INTO `historyevents` VALUES (98,1,2);
INSERT INTO `historyevents` VALUES (99,2,2);
INSERT INTO `historyevents` VALUES (100,3,2);
INSERT INTO `historyevents` VALUES (101,3,2);
INSERT INTO `historyevents` VALUES (102,3,2);
INSERT INTO `historyevents` VALUES (103,1,2);
INSERT INTO `historyevents` VALUES (104,2,2);
INSERT INTO `historyevents` VALUES (105,1,2);
INSERT INTO `historyevents` VALUES (106,1,2);
INSERT INTO `historyevents` VALUES (107,1,2);
INSERT INTO `historyevents` VALUES (108,2,2);
INSERT INTO `historyevents` VALUES (109,1,2);
INSERT INTO `historyevents` VALUES (110,3,2);
UNLOCK TABLES;


DROP TABLE IF EXISTS `jazzartistimage`;
CREATE TABLE `jazzartistimage` (
  `imageId` int(11) NOT NULL,
  `artistId` int(11) NOT NULL,
  KEY `FK_ArtistImageIdToImageId` (`imageId`),
  KEY `FK_ArtistToArtistId` (`artistId`),
  CONSTRAINT `FK_ArtistImageIdToImageId` FOREIGN KEY (`imageId`) REFERENCES `images` (`imageId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ArtistToArtistId` FOREIGN KEY (`artistId`) REFERENCES `artists` (`artistId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `jazzartistimage` WRITE;
/*!40000 ALTER TABLE `jazzartistimage` DISABLE KEYS */;
INSERT INTO `jazzartistimage` VALUES (37,8);
INSERT INTO `jazzartistimage` VALUES (38,8);
INSERT INTO `jazzartistimage` VALUES (39,8);
INSERT INTO `jazzartistimage` VALUES (40,8);
INSERT INTO `jazzartistimage` VALUES (41,9);
INSERT INTO `jazzartistimage` VALUES (42,9);
INSERT INTO `jazzartistimage` VALUES (43,9);
INSERT INTO `jazzartistimage` VALUES (45,15);
INSERT INTO `jazzartistimage` VALUES (46,15);
INSERT INTO `jazzartistimage` VALUES (44,13);
INSERT INTO `jazzartistimage` VALUES (49,17);
INSERT INTO `jazzartistimage` VALUES (50,17);
INSERT INTO `jazzartistimage` VALUES (51,17);
INSERT INTO `jazzartistimage` VALUES (53,19);
INSERT INTO `jazzartistimage` VALUES (54,19);
INSERT INTO `jazzartistimage` VALUES (55,19);
INSERT INTO `jazzartistimage` VALUES (56,21);
INSERT INTO `jazzartistimage` VALUES (57,21);
INSERT INTO `jazzartistimage` VALUES (58,22);
INSERT INTO `jazzartistimage` VALUES (59,22);
INSERT INTO `jazzartistimage` VALUES (61,23);
INSERT INTO `jazzartistimage` VALUES (62,23);
INSERT INTO `jazzartistimage` VALUES (60,23);
INSERT INTO `jazzartistimage` VALUES (63,24);
INSERT INTO `jazzartistimage` VALUES (64,24);
INSERT INTO `jazzartistimage` VALUES (65,25);
INSERT INTO `jazzartistimage` VALUES (66,25);
INSERT INTO `jazzartistimage` VALUES (10,7);
INSERT INTO `jazzartistimage` VALUES (11,7);
INSERT INTO `jazzartistimage` VALUES (12,7);
INSERT INTO `jazzartistimage` VALUES (13,7);
INSERT INTO `jazzartistimage` VALUES (14,1);
INSERT INTO `jazzartistimage` VALUES (15,1);
INSERT INTO `jazzartistimage` VALUES (52,18);
/*!40000 ALTER TABLE `jazzartistimage` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `jazzevents`;
CREATE TABLE `jazzevents` (
  `eventId` int(11) NOT NULL,
  `artistId` int(11) NOT NULL,
  `locationId` int(11) NOT NULL,
  PRIMARY KEY (`eventId`),
  KEY `FK_JazzEventArtistId` (`artistId`),
  KEY `FK_JazzEventLocationId` (`locationId`),
  CONSTRAINT `FK_JazzEventArtistId` FOREIGN KEY (`artistId`) REFERENCES `artists` (`artistId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_JazzEventEventId` FOREIGN KEY (`eventId`) REFERENCES `events` (`eventId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_JazzEventLocationId` FOREIGN KEY (`locationId`) REFERENCES `locations` (`locationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `jazzevents` WRITE;
/*!40000 ALTER TABLE `jazzevents` DISABLE KEYS */;
INSERT INTO `jazzevents` VALUES (1,1,1);
INSERT INTO `jazzevents` VALUES (7,7,1);
INSERT INTO `jazzevents` VALUES (8,1,13);
INSERT INTO `jazzevents` VALUES (19,8,1);
INSERT INTO `jazzevents` VALUES (20,8,13);
INSERT INTO `jazzevents` VALUES (21,9,14);
INSERT INTO `jazzevents` VALUES (22,9,13);
INSERT INTO `jazzevents` VALUES (23,12,1);
INSERT INTO `jazzevents` VALUES (24,12,13);
INSERT INTO `jazzevents` VALUES (26,13,13);
INSERT INTO `jazzevents` VALUES (27,14,12);
INSERT INTO `jazzevents` VALUES (28,15,12);
INSERT INTO `jazzevents` VALUES (29,16,1);
INSERT INTO `jazzevents` VALUES (30,17,1);
INSERT INTO `jazzevents` VALUES (31,18,1);
INSERT INTO `jazzevents` VALUES (32,19,12);
INSERT INTO `jazzevents` VALUES (33,20,12);
INSERT INTO `jazzevents` VALUES (34,20,13);
INSERT INTO `jazzevents` VALUES (35,21,12);
INSERT INTO `jazzevents` VALUES (36,22,1);
INSERT INTO `jazzevents` VALUES (37,23,1);
INSERT INTO `jazzevents` VALUES (38,24,14);
INSERT INTO `jazzevents` VALUES (39,25,14);
INSERT INTO `jazzevents` VALUES (42,13,12);
UNLOCK TABLES;




DROP TABLE IF EXISTS `navigationbaritems`;
CREATE TABLE `navigationbaritems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) NOT NULL,
  `parentNavId` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PageId` (`pageId`),
  KEY `PK_ParentId` (`parentNavId`),
  CONSTRAINT `FK_PageId` FOREIGN KEY (`pageId`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `PK_ParentId` FOREIGN KEY (`parentNavId`) REFERENCES `navigationbaritems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;


LOCK TABLES `navigationbaritems` WRITE;
INSERT INTO `navigationbaritems` VALUES (1,1,NULL,1);
INSERT INTO `navigationbaritems` VALUES (2,4,NULL,2);
INSERT INTO `navigationbaritems` VALUES (4,11,2,202);
INSERT INTO `navigationbaritems` VALUES (5,15,2,203);
INSERT INTO `navigationbaritems` VALUES (10,7,NULL,5);
UNLOCK TABLES;

DROP TABLE IF EXISTS `tickettypes`;
CREATE TABLE `tickettypes` (
  `ticketTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `ticketTypeName` varchar(100) NOT NULL,
  `ticketTypePrice` decimal(6,2) NOT NULL,
  `nrOfPeople` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ticketTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;


LOCK TABLES `tickettypes` WRITE;
INSERT INTO `tickettypes` VALUES (1,'Single',17.50,1);
INSERT INTO `tickettypes` VALUES (2,'Family',60.00,4);
INSERT INTO `tickettypes` VALUES (4,'Jazz - Patronaat Main Hall',15.00,1);
INSERT INTO `tickettypes` VALUES (5,'Jazz - Patronaat Second Hall',10.00,1);
INSERT INTO `tickettypes` VALUES (6,'Jazz - Grote Markt',0.00,1);
INSERT INTO `tickettypes` VALUES (7,'Jazz - One-Day Pass',35.00,1);
INSERT INTO `tickettypes` VALUES (8,'Jazz - All-Day Pass',80.00,1);
INSERT INTO `tickettypes` VALUES (9,'Jazz - Patronaat Third Hall',10.00,1);
UNLOCK TABLES;

DROP TABLE IF EXISTS `ticketlinks`;
CREATE TABLE `ticketlinks` (
  `ticketLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `ticketTypeId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  PRIMARY KEY (`ticketLinkId`),
  KEY `ticketlinks_FK_1` (`eventId`),
  KEY `ticketlinks_FK` (`ticketTypeId`),
  CONSTRAINT `ticketlinks_FK` FOREIGN KEY (`ticketTypeId`) REFERENCES `tickettypes` (`ticketTypeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ticketlinks_FK_1` FOREIGN KEY (`eventId`) REFERENCES `events` (`eventId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='Table that links types of tickets to events for which they can be bought';


LOCK TABLES `ticketlinks` WRITE;
INSERT INTO `ticketlinks` (`ticketLinkId`, `ticketTypeId`, `eventId`) VALUES
(5, 4, 1),   -- Gumbo Kings (Main Hall)
(6, 4, 7),   -- Ntjam Rosie (Main Hall)
(7, 6, 8),   -- Gumbo Kings (Grote Markt)
(13, 4, 19), -- Gare du Nord (Main Hall)
(14, 6, 20), -- Gare du Nord (Grote Markt)
(15, 9, 21), -- The Nordanians (Third Hall)
(16, 6, 22), -- The Nordanians (Grote Markt)
(17, 4, 23), -- Evolve (Main Hall)
(18, 6, 24), -- Evolve (Grote Markt)
(20, 6, 26), -- Wicked Jazz Sounds (Grote Markt)
(21, 5, 27), -- Tom Thomson Assemble (Second Hall)
(22, 5, 28), -- Jonna Fraser (Second Hall)
(23, 4, 29), -- Fox & The Mayors (Main Hall)
(24, 4, 30), -- Uncle Sue (Main Hall)
(26, 5, 32), -- Myles Sanko (Second Hall)
(27, 5, 33), -- Ruis Soundsystem (Second Hall)
(28, 6, 34), -- Ruis Soundsystem (Grote Markt)
(29, 5, 35), -- The Family XL (Second Hall)
(30, 4, 36), -- Rilan & The Bombardiers (Main Hall)
(31, 4, 37), -- Soul Six (Main Hall)
(32, 9, 38), -- Han Bennink (Third Hall)
(33, 9, 39), -- Lilth Merlot (Third Hall)
(36, 5, 42), -- Wicked Jazz Sounds (Second Hall)
(53,1,66),
(55,1,68),
(56,1,69),
(58,2,71),
(59,2,72),
(61,1,74),
(62,2,75),
(63,2,76),
(64,1,77),
(65,2,78),
(66,1,79),
(67,2,80),
(68,1,81),
(69,2,82),
(70,2,83),
(71,2,84),
(72,2,85),
(73,1,86),
(74,1,87),
(75,1,88),
(76,2,89),
(78,2,91),
(79,1,92),
(81,2,94),
(82,1,95),
(83,1,96),
(84,1,97),
(85,1,98),
(86,1,99),
(88,2,101),
(89,2,102),
(90,1,103),
(91,1,104),
(92,2,105),
(93,1,106),
(94,2,107),
(95,1,108),
(96,1,109);
UNLOCK TABLES;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT,
  `orderDate` datetime NOT NULL,
  `customerId` int(11) DEFAULT NULL,
  `isPaid` tinyint(1) NOT NULL,
  PRIMARY KEY (`orderId`),
  KEY `orders_FK` (`customerId`),
  CONSTRAINT `orders_FK` FOREIGN KEY (`customerId`) REFERENCES `customers` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE `orderitems` (
  `orderItemId` int(11) NOT NULL AUTO_INCREMENT,
  `ticketLinkId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  PRIMARY KEY (`orderItemId`),
  KEY `orderitems_FK` (`orderId`),
  KEY `orderitems_FK_1` (`ticketLinkId`),
  CONSTRAINT `orderitems_FK` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orderitems_FK_1` FOREIGN KEY (`ticketLinkId`) REFERENCES `ticketlinks` (`ticketLinkId`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resettokens`;
CREATE TABLE `resettokens` (
  `tokenId` int(11) NOT NULL AUTO_INCREMENT,
  `reset_token` varchar(100) NOT NULL,
  `sendTime` datetime NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`tokenId`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `ticketId` int(11) NOT NULL AUTO_INCREMENT,
  `eventId` int(11) DEFAULT NULL,
  `isScanned` tinyint(1) NOT NULL,
  `orderId` int(11) DEFAULT NULL,
  `basePrice` decimal(6,2) NOT NULL,
  `vat` decimal(3,2) NOT NULL,
  `fullPrice` decimal(6,2) NOT NULL,
  `ticketTypeId` int(11) DEFAULT NULL,
  PRIMARY KEY (`ticketId`),
  KEY `tickets_FK` (`orderId`),
  KEY `tickets_ibfk_1` (`eventId`),
  KEY `tickets_FK_1` (`ticketTypeId`),
  CONSTRAINT `tickets_FK` FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`) ON UPDATE CASCADE,
  CONSTRAINT `tickets_FK_1` FOREIGN KEY (`ticketTypeId`) REFERENCES `tickettypes` (`ticketTypeId`) ON UPDATE CASCADE,
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `events` (`eventId`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `strollhistoryticket`;
CREATE TABLE `strollhistoryticket` (
  `ticketId` int(11) NOT NULL AUTO_INCREMENT,
  `guideId` int(11) DEFAULT NULL,
  PRIMARY KEY (`ticketId`),
  KEY `guideId` (`guideId`),
  CONSTRAINT `strollhistoryticket_ibfk_1` FOREIGN KEY (`ticketId`) REFERENCES `tickets` (`ticketId`),
  CONSTRAINT `strollhistoryticket_ibfk_2` FOREIGN KEY (`guideId`) REFERENCES `guides` (`guideId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `textpages`;
CREATE TABLE `textpages` (
  `textPageId` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`textPageId`),
  CONSTRAINT `FK_PK_Pages` FOREIGN KEY (`textPageId`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `textpages` WRITE;
INSERT INTO `textpages` VALUES (1,'<table style="border-collapse: collapse; width: 100%;" border="0">
    <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
    </colgroup>
    <tbody>
        <tr>
            <td>
                <img src="../img/jpg/homeJazz.jpg" width="690" height="405" alt="Jazz Festival">
            </td>
            <td style="text-align: center;">
                <h2>Lets the countdown begins</h2>
                <p id="countdown">00:00:00:00<br>days hours minutes seconds</p>
            </td>
        </tr>
    </tbody>
</table>

<table style="color: var(--bs-body-color); font-family: var(--bs-body-font-family); font-size: var(--bs-body-font-size); text-align: var(--bs-body-text-align); background-color: var(--bs-body-bg); width: 100%; height: 787px; border-width: 0px;" border="0">
    <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
    </colgroup>
    <tbody>
        <tr style="height: 351px;">
            <td style="height: 351px; border-width: 0px; padding: 12px;">
                <h2 style="text-align: center;">Haarlem Jazz</h2>
                <p>
                    Immerse yourself in the soulful rhythms of Haarlems legendary jazz scene. Every July, the city transforms into a musical paradise with open-air performances in historic squares and intimate club sessions. Discover both established masters and emerging talents across genres from smooth bebop to experimental fusion.<br><br>
                    The crown jewel is the Haarlem Jazz Festival, featuring week-long performances at unique venues including the iconic Grote Markt square and medieval church halls. Evening jam sessions at local hotspots like Jazz Café Duketown keep the music alive until dawn.
                </p>
                <p>
                    <a href="../festival/jazz">
                        <button class="btn btn-primary">Explore Lineup</button>
                    </a>
                </p>
            </td>
            <td style="height: 351px; border-width: 0px; padding-top: 12px; padding-right: 12px; padding-bottom: 12px;">
                <img src="../img/jpg/jazzPerformance.jpg" width="600" height="auto" alt="Live Jazz Performance">
            </td>
        </tr>
        <tr style="height: 48px;">
            <td style="height: 48px; border-width: 0px; padding: 12px;">
                <img src="../img/jpg/historyHaarlem.jpg" width="600" height="auto" alt="Jazz History Archives">
            </td>
            <td style="height: 48px; border-width: 0px; padding: 12px;">
                <h2 style="text-align: center;">Haarlem Heritage</h2>
                <p>
                    Founded in the 10th century along the Spaarne River, Haarlem boasts 800 years of rich heritage. Once a powerful member of the Hanseatic League, the city flourished through medieval wool trade and became a center of art during the Dutch Golden Age. Walk past 17th-century gabled houses, the iconic Grote Kerk, and hidden hofjes courtyards that whisper stories from the past.
                </p>
                <p>
                    <a href="../festival/history-stroll-2">
                        <button class="btn btn-primary">Discover Legacy</button>
                    </a>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<table style="border-collapse: collapse; width: 100%;" border="0">
    <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
    </colgroup>
    <tbody>
        <tr>
            <td>
                <div>
                    <a href="../festival">
                        <div class="card img-fluid nav-tile">
                            <img class="card-img-top" src="../img/jpg/haarlemFestival.jpg" alt="Festival Stage">
                            <div class="card-img-overlay">
                                <p class="card-text w-65 inline-block">Experience 5 Days of Musical Magic</p>
                                <button class="btn btn-primary float-end">View Schedule</button>
                            </div>
                        </div>
                    </a>
                </div>
            </td>
            <td>
                <p>
                        <strong>The Haarlem Summer Festival</strong> transforms the city into a cultural playground each July. Highlights include:
                        <br>• Open-air concerts in medieval squares
                        <br>• Historical walking tours
                        <br>• Craft beer gardens
                        <br><br>This 5-day celebration showcases Haarlems unique blend of historical charm and modern creativity, attracting visitors from across Europe.
                </p>
                <p>
                    <span style="white-space: pre-wrap;">
                        <a href="../festival">
                            <button class="btn btn-primary">Get Tickets</button>
                        </a>
                    </span>
                </p>
            </td>
        </tr>
    </tbody>
</table>
<p>&nbsp;</p>');
INSERT INTO `textpages` VALUES (4,'<table style="border-collapse: collapse; width: 100%; height: auto;">
    <colgroup>
        <col style="width: 50%;">
        <col style="width: 50%;">
    </colgroup>
    <tbody>
        <tr>
            <td>
                <a href="../festival/jazz" aria-invalid="true">
                    <div class="card img-fluid nav-tile">
                        <img class="card-img-top" src="../img/jpg/festivalJazz.jpg" alt="Haarlem Jazz">
                        <div class="card-body">
                            <h5 class="card-title">Haarlem Jazz</h5>
                            <p class="card-text">Join us for an unforgettable experience with some of the best jazz musicians in the world!</p>
                            <button class="btn btn-primary">Learn More</button>
                        </div>
                    </div>
                </a>
            </td>
            <td>
                <a href="../festival/history-stroll-2" aria-invalid="true">
                    <div class="card img-fluid nav-tile">
                        <img class="card-img-top" src="../img/jpg/festivalhistory.jpg" alt="A Stroll Through Haarlem">
                        <div class="card-body">
                            <h5 class="card-title">A Stroll Through Haarlem</h5>
                            <p class="card-text">Explore the rich history and stunning architecture of Haarlem with our guided tours!</p>
                            <button class="btn btn-primary">Learn More</button>
                        </div>
                    </div>
                </a>
            </td>
        </tr>
    </tbody>
</table>

<h2>Upcoming Events</h2>
<div id="events" class="row">
    <div class="event-card">
        <h3>Jazz Night</h3>
        <p>Date: July 27, 2025</p>
        <p>Location: Haarlem City Center</p>
        <a href="../festival/jazz" class="btn btn-primary">Get Tickets</a>
    </div>
    <div class="event-card">
        <h3>Historical Walk</h3>
        <p>Date: July 27, 2025</p>
        <p>Location: Haarlem Historical District</p>
        <a href="../festival/history-stroll-2" class="btn btn-primary">Join Us</a>
    </div>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        color: #343a40;
        margin: 20px;
    }
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: scale(1.05);
    }
    .card-body {
        padding: 15px;
    }
    .btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 0.25rem;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #0056b3;
    }
    .event-card {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 15px;
        margin: 10px;
        text-align: center;
    }
    h2 {
        margin-top: 30px;
        color: #007bff;
    }
</style>');
INSERT INTO `textpages` VALUES (7,'&lt;h1 style=&quot;text-align: center;&quot;&gt;History&lt;/h1&gt;\n&lt;p&gt;Haarlem is a city in the Netherlands and the capital of the province of Noord Holland. The city is located on the river Spaarne and in the Zuid-Kennemerland region. Haarlem first appears in literary sources in the 10th century. In the source, the place is mentioned under the name of &amp;rsquo;Haralem&amp;rsquo;. Archaeological research shows that there was already habitation in the are of Spaarne 1500 years before our era.&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;table style=&quot;border-collapse: collapse; width: 170.94%; height: 491px;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 22.4054%;&quot;&gt;&lt;col style=&quot;width: 77.5951%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr&gt;\n&lt;td&gt;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2023-06-21_at_20.22.57_(1).jpg&quot; alt=&quot;undefined&quot; width=&quot;447&quot; height=&quot;331&quot;&gt;&lt;/td&gt;\n&lt;td&gt;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.22.57.jpg&quot; alt=&quot;undefined&quot; width=&quot;654&quot; height=&quot;327&quot;&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;table style=&quot;border-collapse: collapse; width: 100%; height: 536.352px; border-width: 0px;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 28.7995%;&quot;&gt;&lt;col style=&quot;width: 30.7307%;&quot;&gt;&lt;col style=&quot;width: 40.4705%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr style=&quot;height: 69.125px;&quot;&gt;\n&lt;td style=&quot;height: 69.125px;&quot;&gt;\n&lt;h3&gt;Cafe Brinkman&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 69.125px;&quot;&gt;\n&lt;h3&gt;De Gooth&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 69.125px;&quot;&gt;\n&lt;h3&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;Cafe Gierstraat / Cafe &amp;lsquo;t Kantoor&lt;/span&gt;&lt;/h3&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 440.227px;&quot;&gt;\n&lt;td style=&quot;height: 440.227px;&quot;&gt;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24.jpg&quot; alt=&quot;undefined&quot; width=&quot;392&quot; height=&quot;261&quot;&gt; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;&lt;/td&gt;\n&lt;td style=&quot;height: 440.227px;&quot;&gt;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24_(1).jpg&quot; alt=&quot;undefined&quot; width=&quot;421&quot; height=&quot;280&quot;&gt;&lt;/td&gt;\n&lt;td style=&quot;height: 440.227px;&quot;&gt;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.32.24_(2).jpg&quot; alt=&quot;undefined&quot; width=&quot;556&quot; height=&quot;370&quot;&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 27px;&quot;&gt;\n&lt;td style=&quot;text-align: right; height: 27px;&quot;&gt;Grote Markt 13&lt;/td&gt;\n&lt;td style=&quot;height: 27px; text-align: right;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;Botermarkt 19&lt;/span&gt;&lt;/td&gt;\n&lt;td style=&quot;height: 27px; text-align: right;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;Gierstraat 78&lt;/span&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;table style=&quot;color: var(--bs-body-color); font-family: var(--bs-body-font-family); font-size: var(--bs-body-font-size); font-weight: var(--bs-body-font-weight); text-align: var(--bs-body-text-align); width: 100%; height: 1605.62px;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 52.3041%;&quot;&gt;&lt;col style=&quot;width: 47.6959%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr style=&quot;height: 563px;&quot;&gt;\n&lt;td style=&quot;height: 563px;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;&lt;img src=&quot;../img/jpg/Verwey-Museum-Haarlem-nieuw-logo-en-banieren-LR.jpg&quot; alt=&quot;undefined&quot; width=&quot;451&quot; height=&quot;300&quot;&gt;&lt;/td&gt;\n&lt;td style=&quot;height: 563px;&quot;&gt;\n&lt;h3&gt;&lt;strong&gt;Verwey Museum Haarlem&lt;/strong&gt;&lt;/h3&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;Verwey Museum Haarlem is a museum in Haarlem about the history and cultural heritatge of Haarlem and Zuid-Kennemerland. The city museum is located in the historic center of Haarlem at Groot Heigland 47.&lt;strong&gt;&lt;br&gt;&lt;br&gt;&lt;/strong&gt;The Zuid-Kennemerland Historical Museum Foundation was established on August 25, 1975. Until 1990, the foundation was active as a historical information center. In 2005 the name of the museum was changed to Historical Museum Haarlem.&lt;strong&gt; &lt;br&gt;&lt;br&gt;&lt;/strong&gt;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 315.539px;&quot;&gt;\n&lt;td style=&quot;height: 315.539px;&quot;&gt;\n&lt;h3&gt;&lt;strong&gt;Archeology Museum Haarlem&lt;/strong&gt;&lt;/h3&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;The Archeology Museum Haarlem is a museum in the cellar of the Vleeshal on the Grote Markt in Haarlem, dedicated to promoting interesting and conserving the archeological heritage of Kennemerland. &lt;br&gt;&lt;br&gt;The museum is kept open by a large group of volunteers who gather on Wednesday evenings to discuss, document and clean finds. Discoveries are published monthly by the the volunteers.&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 315.539px;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;&lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.45.15.jpg&quot; alt=&quot;undefined&quot; width=&quot;411&quot; height=&quot;274&quot;&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 363.539px;&quot;&gt;\n&lt;td style=&quot;height: 363.539px;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &lt;img src=&quot;../img/jpg/WhatsApp_Image_2025-06-21_at_20.45.15_(1).jpg&quot; alt=&quot;undefined&quot; width=&quot;390&quot; height=&quot;260&quot;&gt;&lt;/td&gt;\n&lt;td style=&quot;height: 363.539px;&quot;&gt;\n&lt;h3&gt;&lt;strong&gt;Frans Hals Museum&lt;/strong&gt;&lt;/h3&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;The Frans Hals Museum is a museum in the North Holland city of Haarlem, founded in 1862, known as &amp;ldquo;museum of the Golden Age&amp;rdquo;. The collection is based on the rich collection of the city itself, which has been built up since the 16th century.&lt;br&gt;&lt;br&gt;The collection is based on the rich collection of the city itself, which has been built up since the 16th century. The museum holds hunderds of paintinigs, including more than a dozen by Frans Haals, from whom the museum takes its name.&lt;/p&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 363.539px;&quot;&gt;\n&lt;td style=&quot;height: 363.539px;&quot;&gt;\n&lt;h3&gt;&lt;strong&gt;NZH Vervoer Museum&lt;/strong&gt;&lt;/h3&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p&gt;NZH Vervoer Maatschappij (NZH Public Transport Company) has an established name since 1881. From the very start a leading entrepreneur in the public transport sector. Unfortunately recent far reaching developments and many changes have put this to a halt. Fortunately some people realized that NHZ has become relevant of the city&amp;rsquo;s history. These people started to collect everything related to NZH&amp;rsquo;s history.&lt;br&gt;&lt;br&gt;The entrance of the museum, as well as the restrooms are easily accessible for people using wheelchairs.&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 363.539px;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &lt;img src=&quot;../img/jpg/nzhvervoermuseumbussentrams_vervoermuseumtramsbussenhaarlem_3_350_235.jpg&quot; alt=&quot;undefined&quot;&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;');
INSERT INTO `textpages` VALUES (11, '<header class="festival-intro">\n  <h1 class="main-heading">Celebrate the Spirit of Live Music</h1>\n  <p class="subheading">Haarlem\s Premier Summer Music Celebration</p>\n</header>\n\n<section class="festival-description">\n  <p>Each summer, Haarlem comes alive with our international music celebration, presenting an eclectic mix of jazz traditions and contemporary sounds. Our carefully curated program showcases established artists and emerging talents across multiple venues.</p>\n  \n  <p>More than just concerts, we create immersive musical experiences that bridge genres and generations. Through daytime workshops and evening performances, we aim to make exceptional live music accessible to all audiences.</p>\n</section>\n\n<div id="allday-pass" data-kind="jazz"></div>\n<section class="ticket-info">\n  <div class="cta-box">\n    <button class="lineup-button">Explore Full Program</button>\n  </div>\n</section>\n\n<div id="events" data-type="jazz"></div>\n\n<footer class="festival-commitment">\n  <p>Experience music without boundaries - where classic melodies meet modern interpretations</p>\n</footer>');
INSERT INTO `textpages` VALUES (15,'&lt;h1 style=&quot;text-align: center;&quot;&gt;Welcome to The Festival Haarlem&lt;/h1&gt;\n&lt;table style=&quot;border-collapse: collapse; width: 100%;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 50%;&quot;&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr&gt;\n&lt;td&gt;\n&lt;h2&gt;Let&#039;s tour around Haarlem&lt;/h2&gt;\n&lt;p&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;Welcome to a city that is filled with historical monuments, spectacular museums and world-famous art! Cars are not allowed on many streets in Haarlem, which makes it a great city for a tour! &lt;br&gt;&lt;br&gt;We organise tours every day during The Festival Haarlem. &lt;br&gt;&lt;/span&gt;&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;\n&lt;h2&gt;Starting Point&lt;/h2&gt;\n&lt;p&gt;Bavo Church&lt;/p&gt;\n&lt;p&gt;(Age 12+)&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;\n&lt;h2&gt;Time&lt;/h2&gt;\n&lt;p&gt;Every festival day:&lt;/p&gt;\n&lt;ul&gt;\n&lt;li&gt;10:00&lt;/li&gt;\n&lt;li&gt;13:00&lt;/li&gt;\n&lt;li&gt;16:00&lt;/li&gt;\n&lt;/ul&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;div id=&quot;events&quot; data-type=&quot;stroll&quot;&gt;&lt;/div&gt;\n&lt;div data-type=&quot;stroll&quot;&gt;&amp;nbsp;&lt;/div&gt;\n&lt;h2 style=&quot;text-align: center;&quot;&gt;--- During 2.5 hours, you will visit ---&lt;/h2&gt;\n&lt;table style=&quot;border-collapse: collapse; width: 99.1336%; height: 334.4px;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 23.17%;&quot;&gt;&lt;col style=&quot;width: 1.61136%;&quot;&gt;&lt;col style=&quot;width: 33.6735%;&quot;&gt;&lt;col style=&quot;width: 41.4723%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr style=&quot;height: 334.4px;&quot;&gt;\n&lt;td style=&quot;height: 334.4px;&quot;&gt;&lt;img src=&quot;../img/jpg/HaarlemGroteMarkt1.JPG&quot; width=&quot;100%&quot; height=&quot;auto&quot;&gt;&lt;/td&gt;\n&lt;td style=&quot;height: 334.4px;&quot;&gt;&amp;nbsp;&lt;/td&gt;\n&lt;td style=&quot;height: 334.4px;&quot;&gt;\n&lt;h2&gt;St. Bavo Church (A)&lt;/h2&gt;\n&lt;p&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The Sint Bavokerk is the largest church in Haarlem. The St Bavo Church is also called the Grote Kerk and is popularly referred to as &amp;ldquo;the old baaf&amp;rdquo;. The St Bavo church is already mentioned in documents from 1245. &lt;br&gt;&lt;br&gt;Since 1245, the church has expanded to its current size with seven bells and a beautiful tower. To this day, the St Bavo Church is the highest building in Haarlem.&lt;/span&gt;&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 334.4px;&quot;&gt;\n&lt;h2 style=&quot;text-align: center;&quot;&gt;One drink per person&lt;/h2&gt;\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;../img/jpg/brouwerij-restaurant-jopenkerk-haarlem-jopenbier_4082379069.jpg&quot; width=&quot;264&quot; height=&quot;176&quot;&gt;&lt;/p&gt;\n&lt;p style=&quot;text-align: center;&quot;&gt;15 minute break at Jopenkerk&lt;/p&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;table style=&quot;border-collapse: collapse; width: 100%; height: 549.1px;&quot; border=&quot;0&quot;&gt;&lt;colgroup&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;col style=&quot;width: 25%;&quot;&gt;&lt;/colgroup&gt;\n&lt;tbody&gt;\n&lt;tr style=&quot;height: 336.7px;&quot;&gt;\n&lt;td style=&quot;text-align: center; height: 336.7px;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21.jpg&quot; width=&quot;200px&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Grote Markt (B)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 336.7px;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_212.jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;De Hallen (C)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 336.7px;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21(1).jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Proveniershof (D)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 336.7px;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_307.jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Jopenkerk (E)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 185.4px;&quot;&gt;\n&lt;td style=&quot;text-align: center; height: 185.4px;&quot;&gt;\n&lt;p&gt;The market square features several works of art, including a statue honoring Laurenz Janszoon Coster, who is widely credited with inventing printing in the Netherlands.&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 185.4px;&quot;&gt;\n&lt;p&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;De Hallen is a contemporary art museum hosting exhibitions featuring national and international artists. Exhibitions are held three times a year and focus on current developments in the visual arts.&lt;/span&gt;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 185.4px;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The Proveniershof is a unique courtyard area in Haarlem, originally intended for the wealthy bourgeoisie. Its houses differ in appearance from other small courtyards in the area.&lt;/span&gt;&lt;/td&gt;\n&lt;td style=&quot;text-align: center; height: 185.4px;&quot;&gt;\n&lt;p&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The story Jopen begins in the 14th century when Haarlem was one of the most important brewing cities in the Netherlands. &lt;/span&gt;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr style=&quot;height: 27px;&quot;&gt;\n&lt;td style=&quot;height: 27px; text-align: center;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21(2).jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&amp;nbsp;&lt;/p&gt;\n&lt;h3&gt;Waalse Kerk (F)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 27px; text-align: center;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21(3).jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Molen de Adriaan (G)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 27px; text-align: center;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21(4).jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Amsterdamse Poort (H)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;td style=&quot;height: 27px; text-align: center;&quot;&gt;\n&lt;p&gt;&lt;img src=&quot;../img/jpg/Frame_21(5).jpg&quot; width=&quot;200&quot; height=&quot;auto&quot;&gt;&lt;/p&gt;\n&lt;h3&gt;Hof van Bakenes (F)&lt;/h3&gt;\n&lt;/td&gt;\n&lt;/tr&gt;\n&lt;tr&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The Waalse Kerk is a Walloon church that was built in the 14th century. It has an upper gallery that was originally built for the Beguines who lived on the courtyard that still bears their name. &lt;/span&gt;&lt;/td&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;In 1778, a businessman from Amsterdam purchased an old defense tower in Haarlem and received permission to build a windmill on top of it. The tower was subsequently transformed into a windmill.&lt;/span&gt;&lt;/td&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The Amsterdamse Poort is a gate located in Haarlem. It is one of the original gates of the city&#039;s old defensive wall and has been well-preserved over the years. It is a significant part of Haarlem&#039;s history.&lt;/span&gt;&lt;/td&gt;\n&lt;td style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;white-space: pre-wrap;&quot;&gt;The Hofje van Bakenes is located on the Bakenessergracht and has two entrances. The main entrance is located on the Wijde Appelaarsteeg. The courtyard at this location is the oldest one in Haarlem.&lt;/span&gt;&lt;/td&gt;\n&lt;/tr&gt;\n&lt;/tbody&gt;\n&lt;/table&gt;\n&lt;h2 style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/h2&gt;\n&lt;h2 style=&quot;text-align: center;&quot;&gt;--- Haarlem in Maps ---&lt;/h2&gt;\n&lt;p&gt;&lt;iframe src=&quot;https://www.google.com/maps/d/u/0/embed?mid=1R3EC9xY6xPNKRIk0CG3kO20wrgsPMHc&amp;amp;ehbc=2E312F&quot; width=&quot;100%&quot; height=&quot;480&quot;&gt;&lt;/iframe&gt;&lt;/p&gt;\n&lt;div id=&quot;calendar&quot; class=&quot;row&quot; data-calendar-type=&quot;stroll&quot;&gt;&lt;/div&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;');
UNLOCK TABLES;
