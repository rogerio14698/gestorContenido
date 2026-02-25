__Mapa de Dependencias de las migraciones__

gallery_images -> galleries

gallery_image_texts -> gallery_images, idiomas

contents -> galerias (original) y, tras migración, galleries

textos_idiomas -> idiomas, contents, tipo_contenidos

menus -> menus (parent), contents, y (tras update) tipo_contenidos

slide_translations -> slides, idiomas

role_permissions -> roles, permissions

gallery_image_texts, slide_translations, textos_idiomas son tablas de traducciones que dependen de idiomas.
-------------------------------------------------------------
Tablas sin dependencias internas importantes: 
	galleries (creación), galerias (legacy), tipo_contenidos, 
	çimage_configs, slides, roles, permissions, jobs, job_batches.