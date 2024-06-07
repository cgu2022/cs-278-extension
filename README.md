# cs-278-extension

An Flarum extension for my CS 278 project, Spring 2024


# Make sure to do the following:

## Download and Configure the CA Certificates

1. **Download the CA Certificates**:
- Download the `cacert.pem` file from [this link](https://curl.haxx.se/ca/cacert.pem).

2. **Configure PHP to Use the CA Certificates**:
- Locate your `php.ini` file (you can find its location by running `php --ini`).
- Open `php.ini` and add or update the following line to point to the downloaded `cacert.pem` file:
```
curl.cainfo = "C:\path\to\your\cacert.pem"
```
- Save the `php.ini` file and restart your web server.