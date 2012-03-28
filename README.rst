fancyindex
==========

Overview
--------
``fancyindex`` is a simple PHP script to create a directory listing. It was
created after the need to list the contents of a folder but Apaches'
``+Indexes`` option wasn't avaliable on the server. Since making the header
links is done with the option ``FancyIndex`` this script is named
``fancyindex``.

Prerequisites
-------------
A webserver with PHP. Tested on Apache with PHP5.

Installation
------------
Drop the file ``index.php`` into the directory you want to get listed and make
sure the webserver uses it as index file.

Configuration
-------------
PHP requires explicitly setting the timezone before using it. The script uses
``Europe/Berlin`` as timezone. Depending on your location this might need
to get adjusted.

License
-------
Licensed under the terms of the GPL. See the file ``COPYING`` for details.

