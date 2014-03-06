-- Run the following queries to make the sample data 
-- work in virtual host environment

UPDATE ad_banner
SET image_url = REPLACE(image_url, 'http://localhost/izicms/', '/');

UPDATE menu_item
SET
link = REPLACE(link, '/izicms/index.php/', '/');

UPDATE multimedia_file 
SET
image_square = REPLACE(image_square, 'http://localhost/izicms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/izicms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/izicms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/izicms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/izicms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/izicms/', '/');

UPDATE multimedia_set
SET
image_square = REPLACE(image_square, 'http://localhost/izicms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/izicms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/izicms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/izicms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/izicms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/izicms/', '/');

UPDATE news_article 
SET
image_square = REPLACE(image_square, 'http://localhost/izicms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/izicms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/izicms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/izicms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/izicms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/izicms/', '/'),
content = REPLACE(content, 'http://localhost/izicms/', '/');