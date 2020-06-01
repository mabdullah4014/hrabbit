<?php

namespace App\Observers;

use App\Driver;

class DriverObserver
{
    /**
     * Handle the driver "created" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function created(Driver $driver)
    {
        $driver->username = strtolower($driver->name).strtolower($driver->last_name).'_'.$driver->id
        $driver->save();
    }

    /**
     * Handle the driver "updated" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function updated(Driver $driver)
    {
        //
    }

    /**
     * Handle the driver "deleted" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function deleted(Driver $driver)
    {
        //
    }

    /**
     * Handle the driver "restored" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function restored(Driver $driver)
    {
        //
    }

    /**
     * Handle the driver "force deleted" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function forceDeleted(Driver $driver)
    {
        //
    }
}
