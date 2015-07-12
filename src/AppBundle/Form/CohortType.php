<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CohortType extends AbstractType
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
        $builder
            ->add('name')
        ;

        if ($this->isEditForm) {
            $builder->add('isArchived', null, array(
                    'label' => 'Archive this Cohort',
                    'label_attr'  => array('class' => 'checkbox'),
                    'required' => false
                )
            );
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cohort'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_cohort';
    }
}
