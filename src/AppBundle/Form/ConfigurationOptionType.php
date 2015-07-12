<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigurationOptionType extends AbstractType
{
    private $isEditForm;

    public function __construct($isEditForm = false)
    {
        $this->isEditForm = $isEditForm;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nameLabel = $this->isEditForm ? "Name (cannot be changed)" : "Name";

        $builder
            ->add('name', null, array(
                'label'    => $nameLabel,
                'disabled' => $this->isEditForm,
            ))
            ->add('value')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ConfigurationOption'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_configurationoption';
    }
}
