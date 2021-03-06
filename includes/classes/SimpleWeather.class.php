<?php
/*
Name: IT Fosters
Class : TimeZone
Description: Just a simple wrapper to get weather information from Yahoo YQL service.
Author: Raj kumar
Version: 1.0
*/
  
  class SimpleWeather
  {
    protected $result;

    function get_geoplaces_yql($value="Lisbon, PT")
    {
      return "http://query.yahooapis.com/v1/public/yql?q=".urlencode(sprintf("select * from geo.places where text = '%s'", $value))."&format=json";
    }

    function get_weather_yql($woeid, $degrees="c")
    {
      return "http://query.yahooapis.com/v1/public/yql?q=".urlencode(sprintf("select * from weather.forecast where woeid=%d and u='%s'", $woeid, $degrees))."&format=json";
    }

    function api_call($yql)
    {
		$ch = curl_init($yql);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$brandingData = curl_exec($ch);
		curl_close($ch);
		return json_decode($brandingData);
    }

    function __construct($options=array())
    {
      $woeid = 0;

      if (!isset($options['woeid']) || !is_numeric($options['woeid']))
      {
        $object = $this->api_call($this->get_geoplaces_yql($options['place']));
        
        if ($object->query->count > 0)
        {
          if (is_array($object->query->results->place))
          {
            $woeid = $object->query->results->place[0]->woeid;

            $woeid_alternatives = array();

            foreach ($object->query->results->place as $temp_place)
            {
              array_push($woeid_alternatives, array("place" => $temp_place->name,
                                                    "region" => isset($temp_place->admin1->content)?$temp_place->admin1->content:"",
                                                    "country" => isset($temp_place->country->content)?$temp_place->country->content:"",
                                                    "woeid" => $temp_place->woeid));
            }
          }
          else
          {
            $woeid = $object->query->results->place->woeid;
          }
        }
      }
      else
      {
        $woeid = $options['woeid'];
      }

      if ($woeid > 0)
      {
        $object = $this->api_call($this->get_weather_yql($woeid, $options['degrees']));

        $data['woeid'] = $woeid;
        $data['location'] = $object->query->results->channel->location;
        $data['current_condition'] = $object->query->results->channel->item->condition;
        $data['current_condition']->wind = $object->query->results->channel->wind->speed;
        $data['current_condition']->humidity = $object->query->results->channel->atmosphere->humidity;
        $data['forecast_today'] = $object->query->results->channel->item->forecast[0];
        $data['forecast_tomorrow'] = $object->query->results->channel->item->forecast[0];
        
        if (is_array($woeid_alternatives))
        {
          $data['search_alternatives'] = json_encode($woeid_alternatives);
        }

        $this->result = json_encode($data);

      }
      else
      {
        return false;
      }
    }

    function getResult()
    {
      return $this->result ? json_decode($this->result) : null;
    }

  }

?>