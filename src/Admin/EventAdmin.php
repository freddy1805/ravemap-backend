<?php

namespace App\Admin;

use App\Entity\Event;
use App\Form\LocationType;
use App\Service\GeolocationService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class EventAdmin extends AbstractAdmin
{
    /**
     * @var GeolocationService
     */
    private GeolocationService $geolocationService;

    /**
     * EventAdmin constructor.
     * @param $code
     * @param $class
     * @param null $baseControllerName
     * @param GeolocationService $geolocationService
     */
    public function __construct($code, $class, $baseControllerName = null, GeolocationService $geolocationService)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->geolocationService = $geolocationService;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'Admin/Event/edit.html.twig';
        }

        return parent::getTemplate($name);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('event.tab.meta')
            ->with('event.block.meta', [
                'box_class' => false
            ])
            ->add('id', TextType::class, [
                'disabled' => true,
                'label' => 'event.id',
                'required' => false
            ])
            ->add('eventMode', ChoiceType::class, [
                'label' => 'event.event_mode',
                'choices' => [
                    'event.modes.invite' => Event::MODE_INVITE,
                    'event.modes.mod_only' => Event::MODE_MOD_INVITE,
                    'event.modes.private' => Event::MODE_PRIVATE,
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'event.name'
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'event.date',
                'help' => 'event.date_help'
            ])
            ->add('approval', CheckboxType::class, [
                'required' => false,
                'label' => 'event.approval',
                'help' => 'event.approval_help'
            ])
            ->add('maxInvites', NumberType::class, [
                'label' => 'event.max_invites'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'event.description'
            ])
            ->add('location', LocationType::class, [
                'label' => 'event.location',
                'required' => true,
            ])
            ->end()
            ->end()
            ->tab('event.tab.creator')
            ->with('event.block.creator', [
                'box_class' => false
            ])
            ->add('creator', ModelType::class, [
                'label' => false
            ])
            ->end()
            ->end()
            ->tab('event.tab.invites')
            ->with('event.block.invites', [
                'box_class' => false
            ])
            ->add('invites', null, [
                'label' => false,
            ])
            ->end()
            ->end();
    }

    protected function configureShowFields(ShowMapper $formMapper): void
    {
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id', null, ['label' => 'event.id'])
            ->add('name', null, ['label' => 'event.name'])
            ->add('date', null, ['label' => 'event.date'])
            ->add('creator', null, ['label' => 'event.creator'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'event.id'])
            ->add('name', null, [
                'label' => 'event.name',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('approval', TemplateRegistry::TYPE_BOOLEAN, [
                'label' => 'event.approval',
                'editable' => true,
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('maxInvites', TemplateRegistry::TYPE_INTEGER, [
                'label' => 'event.max_invites',
                'editable' => true,
                'header_style' => 'text-align: center',
                'row_align' => 'center',
            ])
            ->add('date', TemplateRegistry::TYPE_DATETIME, [
                'label' => 'event.date',
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
     * @param Event $object
     */
    public function prePersist($object)
    {
        $this->updateLocation($object);
    }

    /**
     * @param Event $object
     */
    public function preUpdate($object)
    {
        $this->updateLocation($object);
    }

    /**
     * @param Event $event
     * @return Event
     */
    protected function updateLocation(Event &$event): Event
    {
        // TODO: REMOVE DUPLICATED CODE
        $location = $event->getLocation();

        if ($administrativeLevel = $this->geolocationService->getAdministrativeLevel($location)) {
            $adminLevelArr = explode(',', $administrativeLevel);
            $location['name'] = trim($administrativeLevel);
            $location['city'] = trim($adminLevelArr[0]);
            $location['country'] = trim($adminLevelArr[1]);
        }

        $event->setLocation($location);

        return $event;
    }
}
