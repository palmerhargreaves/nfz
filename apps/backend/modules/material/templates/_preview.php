<?php if($material->getFilePreview()): ?>
<a target="_blank" href="/uploads/<?php echo Material::FILE_PREVIEW_PATH, '/', $material->getFilePreview() ?>" alt="">скачать</a>
<?php endif; ?>