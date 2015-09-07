<?php

// Entity definition with mandatory field
class Company extends AbstractEntity
{
    const DEFAULT_TEXT = "Loran ipsum";

    protected $_allowedFields = array('lid', 'strategy', 'culture', 'leadership', 'featured', 'published');
    protected $_defaultValues = array('lid', self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, 0, 0);
    protected $_mandatoryFields = array('lid');
}

// Entity definition without mandatory field
class Stats extends AbstractEntity
{
    const DEFAULT_TEXT = "Loran";
    const DEFAULT_NUMBER = 0;

    protected $_allowedFields = array('industry', 'city', 'cstate', 'years_in_bus', 'no_employees');
    protected $_defaultValues = array(self::DEFAULT_NUMBER, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_NUMBER, self::DEFAULT_NUMBER);
}

class ShortStats extends AbstractEntity
{
    const DEFAULT_TEXT = "Loran";
    const DEFAULT_NUMBER = 0;

    protected $_allowedFields = array('industry', 'city', 'cstate', 'no_employees');
    protected $_defaultValues = array(self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_NUMBER);
}

// Entity definition with custom setter
class ShortPerson extends AbstractEntity {
    const PHOTO_FOLDER         =  '/public/company/personal_photo/';
    const DEFAULT_TEXT         =  "default";
    const DEFAULT_IMAGE        =  'default.jpg';

    protected $_allowedFields = array('id', 'first_name', 'last_name', 'branch', 'position', 'photo');
    protected $_defaultValues = array(0 , self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT);
    protected $extensions = array('jpg', 'png', 'gif', self::DEFAULT_TEXT );

    // Abstract entity "ancestor" will first check if field is allowed and create default setter
    // Photo setter
    public function setPhoto($photo)
    {
        // First checking for extension validity, including default value - default values are set before we approach setting ones passed by data array
        if(!in_array($photo, $this->extensions)) {
            // If extension is invalid we throw descriptive exception
            throw new Exception('Extension specified is not valid');
        }
        // if photo extension equals default value it's final value (which will be the path) is set to defined default value
        if($photo == self::DEFAULT_TEXT) {
            $this->_values['photo'] = self::PHOTO_FOLDER . self::DEFAULT_IMAGE;
        } else {
            // If everything is right and we got extension of the file from the database, we are setting up a link to photo
            $this->_values['photo'] = self::PHOTO_FOLDER . $this->_values['id'] . "." . $photo;
        }
    }
}

// Definition of entity collection
class ShortPersonCollection extends AbstractCollection {
    protected $_entityClass = 'ShortPerson';
}



class Branding extends AbstractEntity
{
    const LOGO_FOLDER         = '/public/company/logo/';
    const COVER_FOLDER        = '/public/company/cover/';
    const OFFICE_FOLDER       = '/public/company/office/';
    const INFO_FOLDER         = '/public/company/info/';
    const JOBS_FOLDER         = '/public/company/jobs/';
    const DEFAULT_IMAGE       = 'default.jpg';
    const DEFAULT_COLOR       = '#fff';
    const DEFAULT_EXTENSION   = 'default';
    const COVER_LIMIT         = 3;
    const INFO_IMAGE_LIMIT    = 5;

    protected $_allowedFields = array('lid', 'user_id', 'name', 'logo', 'cover', 'office', 'info', 'jobs', 'color', 'published');
    protected $_defaultValues = array('lid', 'user_id', self::DEFAULT_EXTENSION, self::DEFAULT_EXTENSION, self::DEFAULT_EXTENSION, self::DEFAULT_EXTENSION, self::DEFAULT_EXTENSION, self::DEFAULT_EXTENSION, self::DEFAULT_COLOR, 0);
    protected $_mandatoryFields = array('lid');
    protected $extensions = array('jpg', 'png', 'gif', self::DEFAULT_EXTENSION );


    public function setLogo($logo)
    {
        if(!in_array($logo, $this->extensions)) {
            throw new Exception('Extension specified is not valid');
        }
        if($logo == self::DEFAULT_EXTENSION) {
            $this->_values['logo'] = self::LOGO_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $this->_values['logo'] = self::LOGO_FOLDER . $this->_values['lid'] . "." . $logo;
        }
    }

    public function setCover($cover)
    {
        if ($cover == self::DEFAULT_EXTENSION) {
            $this->_values['cover'][0] = self::COVER_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $covers = json_decode($cover);
            $i = 0;
            foreach($covers as $key => $cover) {
                if (!in_array($cover, $this->extensions)) {
                    throw new Exception('Extension specified is not valid');
                }
                if ($key>=(self::COVER_LIMIT - 1)) {
                    throw new Exception('Number of images exeeds limit of images');
                }
                $this->_values['cover'][$key] = self::COVER_FOLDER . $this->_values['lid'] . '/' . $key . '.' . $cover;
                $i++;
            }
        }
    }

    public function setOffice($office)
    {
        if(!in_array($office, $this->extensions)) {
            throw new Exception('Extension specified is not valid');
        }
        if($office == self::DEFAULT_EXTENSION) {
            $this->_values['office'] = self::OFFICE_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $this->_values['office'] = self::OFFICE_FOLDER . $this->_values['lid'] . "." . $office;
        }
    }

    public function setInfo($info)
    {
        if ($info == self::DEFAULT_EXTENSION) {
            $this->_values['info'][0] = self::INFO_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $info_images = json_decode($info);
            $i = 0;
            foreach($info_images as $key => $info_image) {
                if (!in_array($info_image, $this->extensions)) {
                    throw new Exception('Extension specified is not valid');
                }
                if ($key > (self::INFO_IMAGE_LIMIT - 1)) {
                    throw new Exception('Number of images exeeds limit of images');
                }
                $this->_values['info'][$key] = self::INFO_FOLDER . $this->_values['lid'] . '/' . $key . '.' . $info_image;
                $i++;
            }
        }
    }

    public function setJobs($jobs)
    {
        if(!in_array($jobs, $this->extensions)) {
            throw new Exception('Extension specified is not valid');
        }
        if($jobs == self::DEFAULT_EXTENSION) {
            $this->_values['jobs'] = self::JOBS_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $this->_values['jobs'] = self::JOBS_FOLDER . $this->_values['lid'] . "." . $jobs;
        }
    }

}


class Person extends AbstractEntity {
    const PHOTO_FOLDER         =  '/public/company/personal_photo/';
    const DEFAULT_TEXT         =  "default";
    const DEFAULT_IMAGE        =  'default.jpg';

    protected $_allowedFields = array('id', 'company_id', 'first_name', 'last_name', 'branch', 'position', 'photo', 'qa');
    protected $_defaultValues = array(0 , 0, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, array());
    protected $extensions = array('jpg', 'png', 'gif', self::DEFAULT_TEXT );

    // Abstract entity "ancestor" will first check if field is allowed and create default setter
    // Photo setter
    public function setPhoto($photo)
    {
        // First checking for extension validity, including default value - default values are set before we approach setting ones passed by data array
        if(!in_array($photo, $this->extensions)) {
            // If extension is invalid we throw descriptive exception
            throw new Exception('Extension specified is not valid');
        }
        // if photo extension equals default value it's final value (which will be the path) is set to defined default value
        if($photo == self::DEFAULT_TEXT) {
            $this->_values['photo'] = self::PHOTO_FOLDER . self::DEFAULT_IMAGE;
        } else {
            // If everything is right and we got extension of the file from the database, we are setting up a link to photo
            $this->_values['photo'] = self::PHOTO_FOLDER . $this->_values['id'] . "." . $photo;
        }
    }

    public function setQa($qa)
    {
        if (count($qa) > 0) {
            foreach($qa as $key => $value) {
                $this->_values['qa'][$key]['id']       = $value['id'];
                $this->_values['qa'][$key]['question'] = $value['question'];
                $this->_values['qa'][$key]['answer']   = $value['answer'];
                $this->_values['qa'][$key]['photo']   = $value['photo'];
            }
        }
    }

}


class Perk extends AbstractEntity
{
    const DEFAULT_NUMBER = 0;
    const DEFAULT_TEXT = "Default perk";

    protected $_allowedFields = array('perk_id','perk');
    protected $_defaultValues = array(self::DEFAULT_NUMBER, self::DEFAULT_TEXT);
}


class PerkCollection extends AbstractCollection {
    protected $_entityClass = 'Perk';
}


class FeaturedCompany extends AbstractEntity {
    const FEATURE_COVER_FOLDER  = '/public/company/feature_cover/';
    const LOGO_FOLDER           = '/public/company/logo/';
    const DEFAULT_TEXT          = "default";
    const DEFAULT_IMAGE         = 'default.jpg';
    const DEFAULT_EXTENSION     = 'default';

    protected $_allowedFields = array('lid', 'name', 'industry', 'culture', 'logo', 'feature_cover', 'no_employees', 'city', 'cstate');
    protected $_defaultValues = array('lid', self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT, self::DEFAULT_TEXT);
    protected $extensions = array('jpg', 'png', 'gif', self::DEFAULT_TEXT );

    public function setFeature_cover($feature_cover)
    {
        if(!in_array($feature_cover, $this->extensions)) {
            throw new Exception('Extension specified is not valid');
        }
        if($feature_cover == self::DEFAULT_EXTENSION) {
            $this->_values['feature_cover'] = self::FEATURE_COVER_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $this->_values['feature_cover'] = self::FEATURE_COVER_FOLDER . $this->_values['lid'] . "." . $feature_cover;
        }
    }

    public function setLogo($logo)
    {
        if(!in_array($logo, $this->extensions)) {
            throw new Exception('Extension specified is not valid');
        }
        if($logo == self::DEFAULT_EXTENSION) {
            $this->_values['logo'] = self::LOGO_FOLDER . self::DEFAULT_IMAGE;
        } else {
            $this->_values['logo'] = self::LOGO_FOLDER . $this->_values['lid'] . "." . $logo;
        }
    }

}


class FeaturedCompanyCollection extends AbstractCollection {
    protected $_entityClass = 'FeaturedCompany';
}