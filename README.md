# Co visitation count computation

## Usage

### Add visits
```php
$coVisit = new CoVisitationCounts();

$visitedObject = new CoVisitationCounts\VisitedObject('10');
$visit = new CoVisitationCounts\Visit('userId-12', $visitedObject);

$coVisit->addVisit($visit);

$currentVisitedObject = new CoVisitationCounts\VisitedObject('11');
$currentVisit = new CoVisitationCounts\Visit('userId-16', $currentVisitedObject);

$result = $coVisit->getResult($currentVisit);
```

### Export model
```php

$model = $coVisit->exportModel(new CoVisitationCounts\CoVisitationCountsModel());
$result = $model->getResult($currentVisit);

```

### Process export
```php
/** @var VisitedObjectInterface $item */
foreach ($result as $item) {
    $item->getId(); 
}

```

predictator.eu