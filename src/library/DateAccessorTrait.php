<?php namespace Hokeo\Vessel;
  
// This trait contains a copy and paste of the identically named methods 
// from Laravel's Eloquent model, however anything referring to a Carbon
// instance is replaced with an instance of our own UserCarbon class.

trait DateAccessorTrait
{
 
    public function freshTimestamp()
    {
        return new UserCarbon;
    }
 
    protected function asDateTime($value)
    {
 
        if (is_numeric($value)) {
			
            // was previously:  return Carbon::createFromTimestamp($value);
            return UserCarbon::createFromTimestamp($value);
 
        } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
			
            // was previously:    return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            return UserCarbon::createFromFormat('Y-m-d', $value)->startOfDay();
 
        } elseif (! $value instanceof DateTime) {
			
            $format = $this->getDateFormat();
			
            // was previously:  return Carbon::createFromFormat($format, $value);
            return UserCarbon::createFromFormat($format, $value);
			
        }
 
        // was previously:  return Carbon::instance($value);
        return UserCarbon::instance($value);
    }
 
}