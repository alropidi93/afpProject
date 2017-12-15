<?php
class Thing extends Eloquent {
    /**
     * Hydrate method
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Collection
     */
    static public function hydrate(array $data, $connection = NULL)
    {
        // get calling class so we can hydrate using that type
        $klass = get_called_class();

        // psuedo hydrate
        $collection = new Illuminate\Database\Eloquent\Collection();
        foreach ($data as $raw_obj)
        {
            $model = new $klass;
            $model = $model->newFromBuilder($raw_obj);
            if (!is_null($connection))
                $model = $model->setConnection($connection);
            $collection->add($model);
        }
        return $collection;

    }
}
