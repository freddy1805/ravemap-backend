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

final class StaticPageAdmin extends AbstractAdmin
{

    /**
     * @param string $name
     * @return string
     */
    public function getTemplate($name): string
    {
        switch ($name) {
            case 'edit':
                return 'Admin/Event/edit.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    /**
     * Set default sort values for list-view
     * @param array $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues)
    {
        $sortValues['_page'] = 1;
        $sortValues['_sort_order'] = 'ASC';
        $sortValues['_sort_by'] = 'navPosition';
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('static_page.tab.meta')
            ->with('static_page.block.meta', [
                'box_class' => false,
            ])
            ->add('locale', null, [
                'label' => 'static_page.locale',
                'required' => true,
                'attr' => [
                    'class' => 'locale_switcher'
                ]
            ])
            ->add('title',TextType::class, [
                'required' => true,
                'label' => 'static_page.title_content',
                'attr' => [
                    'class' => 'static_page_title'
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => 'static_page.slug',
                'required' => false,
                'help' => 'static_page.slug_help',
                'disabled' => true,
                'block_prefix' => 'slug',
                'attr' => [
                    'class' => 'static_page_slug'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'static_page.content',
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ])
            ->end()
            ->end()
            ->tab('static_page.tab.nav')
            ->with('static_page.block.nav', [
                'box_class' => false
            ])
            ->add('navPosition', NumberType::class, [
                'label' => 'static_page.nav_position',
                'required' => false
            ])
            ->add('inMainNav', CheckboxType::class, [
                'label' => 'static_page.in_main_nav',
                'required' => false
            ])
            ->add('inFooterNav', CheckboxType::class, [
                'label' => 'static_page.in_footer_nav',
                'required' => false
            ])
            ->end()
            ->end()
            ->tab('static_page.tab.seo')
            ->with('static_page.block.seo', [
                'box_class' => false
            ])
            ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $formMapper): void
    {
        $formMapper;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title', null, ['label' => 'static_page.title_content'])
            ->add('inMainNav', null, ['label' => 'static_page.in_main_nav'])
            ->add('inFooterNav', null, ['label' => 'static_page.in_footer_nav'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('navPosition', TemplateRegistry::TYPE_INTEGER, [
                'label' => 'static_page.nav_position',
                'header_style' => 'text-align: center',
                'editable' => true,
                'row_align' => 'center',
            ])
            ->addIdentifier('title', TemplateRegistry::TYPE_TEXT, [
                'label' => 'static_page.title_content'
            ])
            ->add('inMainNav', TemplateRegistry::TYPE_BOOLEAN, [
                'label' => 'static_page.in_main_nav',
                'header_style' => 'text-align: center',
                'editable' => true,
                'row_align' => 'center',
            ])
            ->add('inFooterNav', TemplateRegistry::TYPE_BOOLEAN, [
                'label' => 'static_page.in_footer_nav',
                'editable' => true,
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
