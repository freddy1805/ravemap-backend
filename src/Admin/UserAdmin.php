<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Object\Metadata;
use Sonata\AdminBundle\Object\MetadataInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class UserAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('user.tab.meta')
            ->with('user.block.meta', [
                'box_class' => false
            ])
            ->add('username', TextType::class, [
                'label' => 'user.username'
            ])
            ->add('email', EmailType::class, [
                'label' => 'user.email'
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
}
