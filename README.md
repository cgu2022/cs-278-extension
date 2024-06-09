# cs-278-extension

A Flarum extension for my CS 278 project, Spring 2024.

All relevant files to the development of this extension are explained in the second section, and each of them contain insightful comments.

1. Setup Instructions
2. CS 278 UniForum Extension Description
3. Installing CS 278 Extension Instructions
4. Developing CS 278 Extension Instructions

----

## Setup Instructions

### Install OpenAI Community's PHP API Accessor Library
To install the necessary dependencies, run the following command:
```bash
composer require openai-php/client guzzlehttp/guzzle
```
> Note: Ensure that PHP version 8.1 or above is already configured on your system for this to work.

### Download and Configure the CA Certificates*

*Note that you only need to do this step if you're doing localhost development or don't have a proper SSL signature. This is step is necessary for sending requests to OpenAI's API.


1. **Download the CA Certificates**:
- Download the `cacert.pem` file from [this link](https://curl.haxx.se/ca/cacert.pem).

2. **Configure PHP to Use the CA Certificates**:
- Locate your `php.ini` file (you can find its location by running `php --ini`).
- Open `php.ini` and add or update the following line to point to the downloaded `cacert.pem` file:
```ini
curl.cainfo = "C:\path\to\your\cacert.pem"
```
- Save the `php.ini` file and restart your web server.

---

## CS 278 UniForum Extension Description

---

### Overview

**UniForum Extension**:
This extension enhances Flarum by integrating OpenAI's GPT API to generate automated summaries of discussion threads. Users can click a button to request a summary of the conversation, which is then displayed to improve user experience by providing concise overviews of lengthy discussions.

### File Structure

```
CS-278_EXTENSION
│ .gitignore
│ composer.json
│ extend.php
│ LICENSE
│ package-lock.json
│ README.md
│
├───js
│ ├───dist
│ │ admin.js
│ │ admin.js.map
│ │ forum.js
│ │ forum.js.map
│ │
│ └───src
│ ├───admin.js
│ └───forum.js
│
├───locale
│ en.yml
│
└───src
├───Api
│ ├───Serializer
│ │ SummarySerializer.php
│ │
│ ├───GenerateSummaryController.php
│ ├───api.php (empty)
│ └───extend.php (empty)
│
├───api.php
└───extend.php
```

## File Summaries and Documentation


#### `composer.json`

**Description**: Composer configuration for PHP dependencies.

**Exposes**:
- Defines PHP dependencies required by the extension.
- Sets up autoloading for the `CGU2022\\CS278Extension` namespace.

---

#### `extend.php`

**Description**: Main entry file to extend Flarum's functionality.

**Exposes**:
- Registers a POST route `/generate-summary` handled by `GenerateSummaryController`.

---

#### `api.php`

**Description**: Defines API endpoints for the extension.

**Exposes**:
- Sets up the POST route `/generate-summary` for API interactions.

---

#### `SummarySerializer.php`

**Description**: Serializes the summary data from GPT API responses.

**Exposes**:
- Converts the summary and full response from GPT API into JSON API format.

---

#### `GenerateSummaryController.php`

**Description**: Handles requests to generate discussion summaries using the GPT API.

**Exposes**:
- Endpoint logic for `/generate-summary`.
- Collects discussion posts and sends them to the GPT API.
- Returns the summary and the full response.

---

#### `js/src/admin.js`

**Description**: Registers admin settings for the extension.

**Exposes**:
- Provides a setting in the admin panel to enter the OpenAI API key.

---

#### `js/src/forum.js`

**Description**: Extends the discussion page to add a "Generate Summary" button.

**Exposes**:
- Adds a button to the discussion page to trigger summary generation.
- Displays a loading indicator while the summary is being generated.
- Handles the API request to generate the summary.


---

## Installing UniForum

To install the extension, run the following command:

```bash
composer require cgu2022/cs-278-extension
```

To update the extension when a new version is released, run:

```bash
composer update
```

**NOTE**: If you are using a Docker image to deploy your UniForum instance, do not use the above commands directly. Instead, add the following line to your `docker_entrypoint.sh` file:

```bash
extension require cgu2022/cs-278-extension
```

This ensures that the extension is properly installed during the Docker container's initialization.

---

## Developing UniForum

Once you have completed the setup and familiarized yourself with the file structure, you can start developing UniForum.

1. **Prepare the Environment**:
   - Create a folder named "packages" in the root directory of your Flarum instance.
   - Run the following commands to set up the Composer repository and require the extension in development mode:

   ```bash
   composer config repositories.0 path "packages/*"
   composer require cgu2022/cs-278-extension *@dev
   ```

2. **Install JavaScript Dependencies**:
   - Navigate to the `js` folder and run the following commands:

   ```bash
   npm install
   npm run dev
   ```

3. **During Development**:
   - **EACH TIME YOU MAKE A CHANGE**:
     - Restart the web server.
     - Run `composer install` and `npm install` if necessary.
     - Relaunch the Node.js development environment by running `npm run dev` again.
     - Disable and then re-enable this extension in the Flarum *administrator* panel to apply changes.

4. **Check Dependencies**:
   - Ensure the `composer.json` file contains all necessary dependencies and configurations for your development environment.

5. **Contributing**:
   - Feel free to submit a pull request if you have any improvements or bug fixes for the extension. Contributions are welcome!

---

### Useful Links

- [Flarum Documentation](https://docs.flarum.org/)
- [Flarum Extension Documentation](https://docs.flarum.org/extend)
- [Flarum API Documentation](https://api.docs.flarum.org/)
