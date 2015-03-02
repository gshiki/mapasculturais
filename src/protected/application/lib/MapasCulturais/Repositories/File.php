<?php
namespace MapasCulturais\Repositories;

use MapasCulturais\App;

class File extends \MapasCulturais\Repository{

    function findByGroup(\MapasCulturais\Entity $owner, $group){
        $app = App::i();

        if(class_exists($owner->getClassName() . 'File')){
            $repo = $app->repo($owner->getClassName() . 'File');
            $result = $repo->findBy(array('owner' => $owner, 'group' => $group));
        }else{
            $result = $this->findBy(array('objectType' => $owner->className, 'objectId' => $owner->id, 'group' => $group));
        }

        $registeredGroup = $app->getRegisteredFileGroup($owner->controllerId, $group);

        if($result && (($registeredGroup && $registeredGroup->unique) || $app->getRegisteredImageTransformation($group) || (!$registeredGroup && !$app->getRegisteredImageTransformation($group))))
            $result = $result[0];


        return $result;
    }

    function findOneByGroup(\MapasCulturais\Entity $owner, $group){
        $result = $this->findOneBy(array('objectType' => $owner->className, 'objectId' => $owner->id, 'group' => $group));

        return $result;
    }

    function findByOwnerGroupedByGroup(\MapasCulturais\Entity $owner){
        $app = App::i();

        if(class_exists($owner->getClassName() . 'File')){
            $repo = $app->repo($owner->getClassName() . 'File');
            $files = $repo->findBy(array('owner' => $owner));
        }else{
            $files = $this->findBy(array('objectType' => $owner->className, 'objectId' => $owner->id));
        }

        $result = array();

        if($files){
            foreach($files as $file){
                $registeredGroup = $app->getRegisteredFileGroup($owner->controllerId, $file->group);
                if($registeredGroup && $registeredGroup->unique){
                    $result[trim($file->group)] = $file;
                }else{
                    if(!key_exists($file->group, $result))
                        $result[trim($file->group)] = array();

                    $result[trim($file->group)][] = $file;
                }
            }
            ksort($result);
        }


        return $result;
    }
}