<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }


    public function configureFields(string $pageName): iterable
    {
       $mediaDir = $this->getParameter('medias_directory');
       $uploadDir = $this->getParameter('uploads_directory');

       yield TextField::new('name');

       yield TextField::new('altText','Texte alternatif');



       $imageField = ImageField::new('filename', 'Media')
           ->setBasePath($uploadDir)
           ->setUploadDir($mediaDir)
           ->setUploadedFileNamePattern('[slug]-[uuid].[extension]');
       if(Crud::PAGE_EDIT == $pageName){
           $imageField->setRequired(false);
       }
       yield $imageField;
    }

}
