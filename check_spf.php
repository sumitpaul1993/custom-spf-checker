function check_spf_valid_or_not($domain_name)
{
    //$domain_record is a object that returns domain records value from our database
    $result = (dns_get_record($domain_name, DNS_TXT));

    //check if spf more than 1
    $count = 0;
    foreach($result as $val)
    {
        if(preg_match("#^v=spf1(.*)$#i", $val['txt']))
        {
            $count++;
        }
    }
    $spf_ip_include_valid .= ($count > 1) ? '0' : '' ;
    foreach($result as $val)
    { 
        //if it is spf record
        //get spf record string
        if(preg_match("#^v=spf1(.*)$#i", $val['txt']))
        {
            $spf_value = explode(" ", $domain_record->spf_record);

            //check valid spf record or not 
            //if match full spf
            if($val['txt'] == $domain_record->spf_record)
            {
                $spf_ip_include_valid .= '1';
            }
            //check indivigual ip4 and include part
            else
            {
                    //check spf version start with
                if(@preg_match("#^v=spf1(.*)$#i", $val['txt']))
                {
                    $spf_ip_include_valid .= '1';
                }
                else
                {
                    $spf_ip_include_valid .= '0';
                }

                // check spf version end with
                if(substr( $val['txt'], -strlen( '~all' ) ) == '~all' || substr( $val['txt'], -strlen( '+all' ) ) == '+all' || substr( $val['txt'], -strlen( '-all' ) ) == '-all' )
                {
                    $spf_ip_include_valid .= '1';
                }
                else
                {
                    $spf_ip_include_valid .= '0';
                }
                foreach($spf_value as $spf)
                {
                    //check ip4 part
                    if(@preg_match("#^ip4(.*)$#i", $spf))
                    {
                        //check ip record valid or not 
                        if(@preg_match("/{$spf}/i", $val['txt']))
                        {
                            $spf_ip_include_valid .= '1';
                        }
                        else
                        {
                            $spf_ip_include_valid .= '0';
                        }
                    }

                    //check ip6 part
                    if(@preg_match("#^ip6(.*)$#i", $spf))
                    {
                        //check ip record valid or not 
                        if(@preg_match("/{$spf}/i", $val['txt']))
                        {
                            $spf_ip_include_valid .= '1';
                        }
                        else
                        {
                            $spf_ip_include_valid .= '0';
                        }
                    }

                    //check include part
                    if(@preg_match("#^include(.*)$#i", $spf))
                    {
                        //check ip record valid or not 
                        if(@preg_match("/{$spf}/i", $val['txt']))
                        {
                            $spf_ip_include_valid .= '1';
                        }
                        else
                        {
                            $spf_ip_include_valid .= '0';
                        }
                    }
                }
                

            }
        }
    }
    if(strpos($spf_ip_include_valid, '0') || @preg_match("#^0(.*)$#i", $spf_ip_include_valid))
    {
        $valid = 0;
    }
    else
    {
        $valid=1;
    }
    return $valid;
}

//end of spf checker helper

