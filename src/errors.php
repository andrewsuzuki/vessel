<?php

if (!class_exists('VesselFrontNotFoundException'))
{
	class VesselFrontNotFoundException extends \Exception {}
}

if (!class_exists('VesselBackNotFoundException'))
{
	class VesselBackNotFoundException extends \Exception {}
}