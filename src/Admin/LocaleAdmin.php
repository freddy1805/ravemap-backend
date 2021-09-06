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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class LocaleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', null, [
                'label' => 'locale.name',
                'required' => true,
            ])
            ->add('localeId',TextType::class, [
                'required' => true,
                'label' => 'locale.locale_id',
                'help' => 'locale.locale_id_help'
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'locale.enabled',
                'help' => 'locale.enabled_help',
                'required' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $formMapper): void
    {
        $formMapper;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'locale.name'])
            ->add('localeId', null, ['label' => 'locale.locale_id'])
            ->add('enabled', null, ['label' => 'locale.enabled'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name', TemplateRegistry::TYPE_TEXT, [
                'label' => 'locale.name'
            ])
            ->add('localeId', TemplateRegistry::TYPE_TEXT, [
                'label' => 'locale.locale_id',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('enabled', TemplateRegistry::TYPE_BOOLEAN, [
                'label' => 'locale.enabled',
                'header_style' => 'text-align: center',
                'editable' => true,
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
