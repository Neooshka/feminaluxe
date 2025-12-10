const Ziggy = {"url":"https:\/\/feminaluxe","port":null,"defaults":{},"routes":{"filament.exports.download":{"uri":"filament\/exports\/{export}\/download","methods":["GET","HEAD"],"parameters":["export"],"bindings":{"export":"id"}},"filament.imports.failed-rows.download":{"uri":"filament\/imports\/{import}\/failed-rows\/download","methods":["GET","HEAD"],"parameters":["import"],"bindings":{"import":"id"}},"livewire.update":{"uri":"livewire\/update","methods":["POST"]},"livewire.upload-file":{"uri":"livewire\/upload-file","methods":["POST"]},"livewire.preview-file":{"uri":"livewire\/preview-file\/{filename}","methods":["GET","HEAD"],"parameters":["filename"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
