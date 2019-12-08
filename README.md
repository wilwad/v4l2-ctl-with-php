# v4l2-ctl-with-php
Changing device settings usings v4l2-ctl and PHP

After reading this article here https://www.kurokesu.com/main/2016/01/16/manual-usb-camera-settings-in-linux/
I decided to create a PHP interface for controlling UVC-compatible devices using V4l2 on Linux.

Requirements

Must have v4l2-ctl installed already by 

sudo apt update
sudo apt-get install v4l-utils

Usage

Start PHP test server
php -S localhost:8080
