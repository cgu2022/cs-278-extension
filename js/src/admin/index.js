// Import the necessary Flarum admin app module.
import app from 'flarum/admin/app';

// Register a new initializer for the extension with the unique ID 'cgu2022-cs-278-extension'.
app.initializers.add('cgu2022-cs-278-extension', () => {
  // Access the extensionData object to register new settings specific to this extension.
  app.extensionData
    // Specify which extension these settings belong to using the unique extension ID.
    .for('cgu2022-cs-278-extension')
    // Register a new setting field in the admin panel for the OpenAI API key.
    .registerSetting({
      // Define the setting key that will be used in the settings table.
      setting: 'cgu2022.cs-278-extension.api_key',
      // Set the label for the setting field that will be displayed in the admin panel.
      label: app.translator.trans('cgu2022-cs-278-extension.admin.settings.api_key_label'),
      // Specify the type of the input field as a text box.
      type: 'text',
      // Provide a placeholder text for the input field to guide the admin.
      placeholder: 'Enter your OpenAI API key',
    });
});
