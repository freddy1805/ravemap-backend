<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Object\Metadata;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class UserAdmin extends AbstractAdmin
{
    /**
     * @var ImageProvider
     */
    protected ImageProvider $imageProvider;

    /**
     * UserAdmin constructor.
     * @param $code
     * @param $class
     * @param null $baseControllerName
     * @param ImageProvider $imageProvider
     */
    public function __construct($code, $class, $baseControllerName = null, ImageProvider $imageProvider)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->imageProvider = $imageProvider;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        /** @var User $user */
        $user = $this->getSubject();
        $image = $user->getImage();
        $description = "";
        if ($image && ($webPath = $this->imageProvider->generatePublicUrl($image, 'user_image_medium'))) {
            $description =  '<img src="'.$webPath.'" />';
        }

        $formMapper
            ->tab('user.tab.meta')
            ->with('user.block.meta', [
                'box_class' => false,
                'description' => $description
            ])
            ->add('image',MediaType::class, [
                'required' => false,
                'label' => 'user.image',
                'by_reference' => true,
                'context' => 'user_image',
                'provider' => 'sonata.media.provider.image',
            ])
            ->add('username', TextType::class, [
                'label' => 'user.username'
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.email'
            ])
            ->add('lastLogin', DateTimeType::class, [
                'label' => 'user.last_login',
                'required' => false,
                'disabled' => true
            ])
            ->add('registeredAt', DateTimeType::class, [
                'label' => 'user.registered_at',
                'required' => false,
                'disabled' => true
            ])
            ->add('raverScore', NumberType::class, [
                'label' => 'user.raver_score'
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'user.password',
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'user.enabled'
            ])
            ->end()
            ->end()
            ->tab('user.tab.roles')
            ->with('user.block.roles', [
                'box_class' => false
            ])
            ->add('roles', CollectionType::class, [
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'allow_extra_fields' => true,
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        'user.roles.user' => 'ROLE_USER',
                        'user.roles.api_user' => 'ROLE_API',
                        'user.roles.admin' => 'ROLE_ADMIN',
                        'user.roles.supervisor' => 'ROLE_SUPER_ADMIN',
                    ]
                ]
            ])
            ->end()
            ->end();
    }

    protected function configureShowFields(ShowMapper $formMapper): void
    {
        $formMapper
            ->with('user.block.meta', [
                'class' => 'col-md-6'
            ])
            ->add('id', TemplateRegistry::TYPE_STRING, [
                'label' => 'user.id'
            ])
            ->add('username', TemplateRegistry::TYPE_STRING, [
                'label' => 'user.username'
            ])
            ->add('email', TemplateRegistry::TYPE_EMAIL, [
                'label' => 'user.email'
            ])
            ->add('raverScore', TemplateRegistry::TYPE_INTEGER, [
                'label' => 'user.raver_score'
            ])
            ->add('lastLogin', TemplateRegistry::TYPE_DATETIME, [
                'label' => 'user.last_login',
                'format' => 'd. M Y, H:i'
            ])
            ->end()
            ->with('user.block.roles', [
                'box_class' => 'box box-danger',
                'class' => 'col-md-6'
            ])
            ->add('roles', TemplateRegistry::TYPE_ARRAY, [
                'label' => 'user.tab.roles',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('username', null, ['label' => 'user.username'])
            ->add('email', null, ['label' => 'user.email'])
            ->add('raverScore', null, ['label' => 'user.raver_score'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('username', TemplateRegistry::TYPE_TEXT, [
                'label' => 'user.username'
            ])
            ->add('raverScore', TemplateRegistry::TYPE_INTEGER, [
                'label' => 'user.raver_score',
                'header_style' => 'text-align: center',
                'editable' => true,
                'row_align' => 'center',
            ])
            ->add('email', TemplateRegistry::TYPE_EMAIL, [
                'label' => 'user.email',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('enabled', TemplateRegistry::TYPE_BOOLEAN, [
                'label' => 'user.enabled',
                'editable' => true,
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('lastLogin', TemplateRegistry::TYPE_DATETIME, [
                'label' => 'user.last_login',
                'format' => 'd. M Y, H:i',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('registeredAt', TemplateRegistry::TYPE_DATETIME, [
                'label' => 'user.registered_at',
                'format' => 'd. M Y, H:i',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('_action', null, [
                'label' => 'user.actions',
                'header_style' => 'text-align: right',
                'row_align' => 'right',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param User $object
     */
    public function prePersist($object)
    {
        if ($image = $object->getImage()) {
            $image->setAuthorName($object->getUsername());
            $object->setImage($image);
        }
    }

    /**
     * @param User $object
     */
    public function preUpdate($object)
    {
        if ($image = $object->getImage()) {
            $image->setAuthorName($object->getUsername());
            $object->setImage($image);
        }
    }

    /**
     * @param User $object
     * @return Metadata
     */
    public function getObjectMetadata($object)
    {
        $media = $object->getImage();

        $url = '';
        if ($media) {
            $url = $this->imageProvider->generatePublicUrl($media, $this->imageProvider->getFormatName($media, 'admin'));
        }

        return new Metadata($object->getUsername(), $object->getEmail(), $url);
    }
}
