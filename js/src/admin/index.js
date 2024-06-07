import app from 'flarum/admin/app';

app.initializers.add('cgu2022-cs-278-extension', () => {
  app.extensionData
    .for('cgu2022-cs-278-extension')
    .registerSetting({
      setting: 'cgu2022.cs-278-extension.api_key',
      label: app.translator.trans('cgu2022-cs-278-extension.admin.settings.api_key_label'),
      type: 'text',
      placeholder: 'Enter your OpenAI API key',
    });
});
