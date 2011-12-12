<?php

/**
 * BaseBlog
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $Name
 * @property string $Naviname
 * @property integer $Language_id
 * @property Doctrine_Collection $Blogpost
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBlog extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('blog');
        $this->hasColumn('Name', 'string', 128, array(
             'type' => 'string',
             'length' => '128',
             ));
        $this->hasColumn('Naviname', 'string', 128, array(
             'type' => 'string',
             'length' => '128',
             ));
        $this->hasColumn('Language_id', 'integer', null, array(
             'type' => 'integer',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_hungarian_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Blogpost', array(
             'local' => 'id',
             'foreign' => 'Blog_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $sluggable0 = new Doctrine_Template_Sluggable(array(
             'unique' => true,
             'fields' => 
             array(
              0 => 'Name',
             ),
             'canUpdate' => true,
             'builder' => 
             array(
              0 => 'Konekt_Blog_Model_Inflector',
              1 => 'urlize',
             ),
             ));
        $this->actAs($timestampable0);
        $this->actAs($sluggable0);
    }
}