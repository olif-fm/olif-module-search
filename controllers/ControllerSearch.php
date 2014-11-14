<?php
/**
 * ControllerSearch
 * @version V 1.7
 * @copyright Alberto Vara (https://github.com/avara1986) & Jose Luis Represa (https://github.com/josex2r)
 * @package OLIF.Module.ControllerSearch
 *
 * Módulo para aplicar filtros a los listados
 * DEPENDS/REQUIRED:
 * + controllers/ControllerApp
 */
namespace Olif;

require_once CORE_ROOT . CONTROLLERS . DIRECTORY_SEPARATOR . "ControllerApp.php";

class ControllerSearch extends ControllerApp
{

    private $searchControlName;

    private $searchControlValue;

    private $searchFilters;

    private $searchParams;

    public function __construct()
    {
        $this->searchControlName = "searchControl";
        $this->searchControlValue = "searching";

        $this->searchFilters = array();
        $this->searchParams = array();

        $this->getControllerSession();
        $this->getControllerPage();
        $this->getControllerRequest();
    }

    public function setFilter($inputName, $sessionName, $templateName, $sqlCond, $paramsArray)
    {
        $searchingValue = $this->req->getVar($inputName);
        if ($searchingValue == 'del') {
            $this->session->set($sessionName, "");
            return false;
        }
        if (is_array($searchingValue) || strlen($searchingValue) > 0) {

            $this->session->set($sessionName, json_encode($searchingValue));
        } else {

            if ($this->req->getVar($this->searchControlName) == $this->searchControlValue)
                $this->session->set($sessionName, "");
            else
                $searchingValue = json_decode($this->session->get($sessionName));
        }

        if (is_array($searchingValue))
            $this->page->assignVar($templateName, json_encode($searchingValue), false);
        else
            $this->page->assignVar($templateName, $searchingValue);

        if ($searchingValue != "") {

            $this->searchFilters[$inputName] = $sqlCond;

            if (is_array($paramsArray)) {
                $this->searchParams[$inputName] = array();

                foreach ($paramsArray as $param) {

                    // Array input
                    if (is_array($searchingValue)) {

                        foreach ($searchingValue as $key => $subparam) {
                            $this->searchParams[$inputName][] = $subparam;
                            if ($key != 0)
                                $this->searchFilters[$inputName] = $this->searchFilters[$inputName] . " OR " . $sqlCond;
                        }
                    } else {
                        if ($param == "%")
                            $this->searchParams[$inputName][] = "%" . $searchingValue . "%";
                        elseif ($param == "")
                            $this->searchParams[$inputName][] = $searchingValue;
                        else
                            $this->searchParams[$inputName][] = $searchingValue;
                    }
                }
            }
        }
    }

    public function getQuery($strict = true, $haveSeparator = true)
    {
        if ($this->isSearching()) {
            $separator = $strict ? ") AND " : ") OR ";
            if ($haveSeparator) {
                return "(" . implode($separator . "(", $this->searchFilters) . $separator;
            } else {
                return "(" . implode($separator . "(", $this->searchFilters) . ")";
            }
        } else
            return "";
    }

    public function getParam($inputName)
    {
        if (count($this->searchParams[$inputName]) > 1)
            return $this->searchParams[$inputName];
        else
            return $this->searchParams[$inputName][0];
    }

    public function getParams()
    {
        $params = array();
        foreach ($this->searchParams as $param) {
            if (is_array($param))
                $params = array_merge_recursive($params, $param);
            else
                $params[] = $param;
        }
        return $params;
    }

    public function isSearching()
    {
        $isSearching = (count($this->searchFilters) > 0 && count($this->searchParams) > 0);
        $this->page->assignVar("IS_SEARCHING", $isSearching);
        return $isSearching;
    }
}
