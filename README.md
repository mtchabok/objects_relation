# PHP Objects Relation
PHP Objects Relation for control relations between objects.

Installation
------------

This package is listed on [Packagist](https://packagist.org/packages/mtchabok/objects_relation).

```
composer require mtchabok/objects_relation
```


How To Usage
------------

#### Create Relation Class ####
```php
use \Mtchabok\ObjectsRelation\Relation;
class myObjectUseTraitClass{
    use Relation;
    
    public $name = '';
    
    public function __construct()
    {
        $this->addRelationType('parent', 'child');
        $this->addRelationType('child', 'parent');
    }
}
```
OR
```php
use \Mtchabok\ObjectsRelation\RelationObject;
class myObjectExtendsClass extends RelationObject{
    public $name = '';
    
    public function __construct()
    {
        $this->addRelationType('parent', 'child');
        $this->addRelationType('child', 'parent');
    }
}
```
OR
```php
use \Mtchabok\ObjectsRelation\Relation;
use \Mtchabok\ObjectsRelation\RelationChild;
use \Mtchabok\ObjectsRelation\RelationParent;
class myObjectParentChildTraitClass{
    use Relation;
	use RelationParent{ RelationParent::__construct as __parentConstruct; }
	use RelationChild{ RelationChild::__construct as __childConstruct; }
	
    public $name = '';
    
    public function __construct()
    {
        $this->__parentConstruct();
        $this->__childConstruct();
    }
}
```

#### How Used Relation Object`s ####
```php
$ObjectUseTraitClass1 = new myObjectUseTraitClass();
$ObjectUseTraitClass2 = new myObjectExtendsClass();

$ObjectUseTraitClass1->name = 'Cambyses';
$ObjectUseTraitClass2->name = 'Cyrus';

$ObjectUseTraitClass1->addRelation($ObjectUseTraitClass2, 'child');

echo "{$ObjectUseTraitClass2->name} (parent: {$ObjectUseTraitClass2->getRelation('', 'parent')->name})"; // print Cyrus (parent: Cambyses)
echo PHP_EOL;
echo "{$ObjectUseTraitClass1->name} (child: {$ObjectUseTraitClass1->getRelation('', 'child')->name})"; // print Cambyses (child: Cyrus) 
```
OR
```php
$ObjectUseTraitClass1 = new myObjectParentChildTraitClass();
$ObjectUseTraitClass2 = new myObjectParentChildTraitClass();

$ObjectUseTraitClass1->name = 'Cambyses';
$ObjectUseTraitClass2->name = 'Cyrus';

$ObjectUseTraitClass1->setChild($ObjectUseTraitClass2);

echo "{$ObjectUseTraitClass2->name} (parent: {$ObjectUseTraitClass2->getParent()->name})"; // print Cyrus (parent: Cambyses)
echo PHP_EOL;
echo "{$ObjectUseTraitClass1->name} (child: {$ObjectUseTraitClass1->getChild()->name})"; // print Cambyses (child: Cyrus) 
```

#### Professional Used Relation Object`s ####
This code sample is The Cyrus the Great Family
```php
use \Mtchabok\ObjectsRelation\Relation;

class myObjectFamily{
	use Relation;

	public $sex = '';
	public $name = '';

	public function __construct($name, $sex)
	{
		$this->addRelationType(['father','mother','husband','wife','son','daughter'], [$this, '_relationReturnType']);
		$this->name = $name;
		$this->sex = $sex;
	}

	protected function _relationReturnType($type, $relation)
	{
		switch ($type){
			case 'father': case 'mother':
				return $relation->sex=='male' ?'son' :'daughter';
				break;
			case 'son':case 'daughter':
				return $this->sex=='male' ?'father' :'mother';
				break;
			case 'husband':
				return 'wife';
				break;
			case 'wife':
				return 'husband';
				break;
		}
		return '';
	}
}

$Cambyses1  = new myObjectFamily('Cambyses1', 'male');
$Mandane    = new myObjectFamily('Mandane', 'female');
$Cyrus      = new myObjectFamily('Cyrus', 'male');
$Cassandane = new myObjectFamily('Cassandane', 'female');
$Cambyses2  = new myObjectFamily('Cambyses2', 'male');
$Atossa     = new myObjectFamily('Atossa', 'female');
$Bardiya    = new myObjectFamily('Bardiya', 'male');
$Roksana    = new myObjectFamily('Roksana', 'female');

// add $Mandane by type(wife) into $Cambyses1
$Cambyses1->addRelation($Mandane, 'wife');
// add $Cyrus by type(son) into $Cambyses1
$Cambyses1->addRelation($Cyrus, 'son');

// add $Cyrus by type(son) into $Mandane
$Mandane->addRelation($Cyrus, 'son');

// add $Cassandane by type(wife) into $Cyrus
$Cyrus->addRelation($Cassandane, 'wife');
// add $Cambyses2 and $Bardiya by type(son) into $Cyrus
$Cyrus->addRelation([$Cambyses2, $Bardiya], 'son');
// add $Atossa and $Roksana by type(daughter) into $Cyrus
$Cyrus->addRelation([$Atossa, $Roksana], 'daughter');

// add $Cambyses2 and $Bardiya by type(son) into $Cassandane
$Cassandane->addRelation([$Cambyses2, $Bardiya], 'son');
// add $Atossa and $Roksana by type(daughter) into $Cassandane
$Cassandane->addRelation([$Atossa, $Roksana], 'daughter');

echo "Cyrus (father: {$Cyrus->getRelation('', 'father')->name}, mother: {$Cyrus->getRelation('', 'mother')->name})".PHP_EOL; // print Cyrus (father: Cambyses1, mother: Mandane)
echo "Cyrus wife: {$Cyrus->getRelation('', 'wife')->name}".PHP_EOL; // print Cyrus wife: Cassandane
echo 'Cyrus childes:';
foreach ($Cyrus->getRelations(['daughter','son']) as $child)
	echo " $child->name,";
echo PHP_EOL; // print Cyrus childes: Atossa, Roksana, Cambyses2, Bardiya,
```

#### For More Usage Documentation, Use This Relation Library By IDE ####
