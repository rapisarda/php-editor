### Edit class

```php
use PhpEditor\PsrFile;
use PhpEditor\Node\AnnotationNode;
use App\Entity\Cart;

$file = new PsrFile('src/Entity/AwesomeEntity.php');

// create annotation @ORM\ManyToOne(targetEntity="App\Entity\Cart", cascade={"all"}, fetch="LAZY")
$annotation = new AnnotationNode('ORM\ManyToOne', [
    'targetEntity' => App\Entity\Cart::class, 
    'cascade' => ['all'],
    'fetch' => 'LAZY',
]);

// get the file node
$node = $file->node();

//add use statement
$node->addUse('Doctrine\ORM\Mapping as ORM');

// get class interface or trait
$class = $node->getClassLike();

//add your annotation to $height property
$class->getProperty('$height')
    ->getDocComment()
    ->addStatement($annotation);

// save modifications
$file->save();
```

### Create a class 
```php
use PhpEditor\PsrFile;
use PhpEditor\Node\ClassLikeNode;
use PhpEditor\Node\MethodNode;
use Symfony\Component\HttpFoundation\Response;

$class = new ClassLikeNode();
$class->setName('AwesomeController');
$action = new MethodNode();
$action
    ->setName('awesomeAction')
    ->setBody("
        return new Response('hello world');    
    ");
    
$class->addMethod($action);
    
$file = new PsrFile('src/Controller/AwesomeController.php');
$node = $file->node();
$node->setClassLike($class);
$node->addUse(Response::class);

$file->save();
```