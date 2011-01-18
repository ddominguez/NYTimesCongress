<?php
/**
 * NY Times Congress API PHP Class
 * API Documentation: http://developer.nytimes.com/docs/congress_api
 *
 * @author David Dominguez - http://www.ddominguez.us
 */
 
class NYTimesCongress {

	private $apikey = null;
	private $apiversion = null;
	private $format = null;

    public function __construct($apikey,$apiversion,$format='xml')
    {
		$this->apikey = $apikey;
		$this->format = $format;
		$this->apiversion = $apiversion;
		$this->uri = 'http://api.nytimes.com/svc/politics/'.$this->apiversion.'/us/legislative/congress';
    }
	
	/**
	 * Get a list of members of a particular chamber in a particular Congress.
	 *
	 * @param string $congressnumber
	 * @param string $chamber
	 * @param array $params - optional paramaters (string state, string disrict)
	 */
	public function getMembersLists($congressnumber,$chamber,$params=null)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/members.'.$this->format,$params);
	}
	
	/**
	 * Get biographical and Congressional role information for a particular member of Congress.
	 *
	 * @param int $memberid
	 */
	public function getMemberBio($memberid)
	{
		return $this->_serviceCall($this->uri.'/members/'.$memberid.'.'.$this->format);
	}
	
	/**
	 * Get a list of the most recent new members of the current Congress
	 */
	public function getNewMembers()
	{
		return $this->_serviceCall($this->uri.'/members/new.'.$this->format);
	}
	
	/**
	 * Get the current members of Congress for a particular chamber, state and (for House requests) district.
	 *
	 * @param string $chamber - house, senate
	 * @param string $state - Two-letter state abbreviation
	 * @param string $district - House requests only
	 */
	public function getCurrentMembersByStateDistrict($chamber,$state,$district=null)
	{
		$districtparam = ($chamber=='house' && $district!='') ? '/'.$district : '';
		
		return $this->_serviceCall($this->uri.'/members/'.$chamber.'/'.$state.$districtparam.'/current.'.$this->format);
	}
	
	/**
	 * Get a list of members who have left the Senate or House or have announced plans to do so.
	 *
	 * @param int $congressnumber - The number of the Congress during which the members served (Current Congress for now)
	 * @param string $chamber - house, senate
	 */
	public function getMembersLeavingOffice($congressnumber,$chamber)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/members/leaving.'.$this->format);
	}
	
	/**
	 * Get the most recent vote positions for a specific member of the House of Representatives or Senate.
	 *
	 * @param int $memberid
	 */
	public function getMembersVotePositions($memberid)
	{
		return $this->_serviceCall($this->uri.'/members/'.$memberid.'/votes.'.$this->format);
	}
	
	/**
	 * Compare two members' vote positions in a particular Congress and chamber.
	 * Responses include four calculated values, showing the number and percentage of
	 * votes in which the members took the same position or opposing positions.
	 *
	 * @param int $fmemberid - ID of the first member you want to compare
	 * @param int $smemberid - ID of the second member you want to compare
	 * @param int $congressnumber - Senate(101–111), House(102–111)
	 * @param string $chamber - house, senate
	 */
	public function getMembersVoteComparison($fmemberid,$smemberid,$congressnumber,$chamber)
	{
		return $this->_serviceCall($this->uri.'/members/'.$fmemberid.'/votes/'.$smemberid.'/'.$congressnumber.'/'.$chamber.'.'.$this->format);
	}
	
	/**
	 * Get bill cosponsorship data for a particular member.
	 *
	 * @param int $memberid
	 * @param string $type - cosponsored, withdrawn
	 */
	public function getMemberCosponsoredBills($memberid,$type)
	{
		return $this->_serviceCall($this->uri.'/members/'.$memberid.'/bills/'.$type.'.'.$this->format);
	}
	
	/**
	 * Compare bill sponsorship between two members who served in the same Congress and chamber.
	 *
	 * @param int $fmemberid - ID of the first member you want to compare
	 * @param int $smemberid - ID of the second member you want to compare
	 * @param int $congressnumber - The number of the Congress during which the members served (105–111)
	 * @param string $chamber - house, senate
	 */
	public function getMemberSponsorshipComparison($fmemberid,$smemberid,$congressnumber,$chamber)
	{
		return $this->_serviceCall($this->uri.'/members/'.$fmemberid.'/bills/'.$smemberid.'/'.$congressnumber.'/'.$chamber.'.'.$this->format);
	}
	
	/**
	 * Get information about a particular member's appearances on the House or Senate floor.
	 *
	 * @param int $memberid
	 */
	public function getMemberFloorAppearances($memberid)
	{
		return $this->_serviceCall($this->uri.'/members/'.$memberid.'/floor_appearances.'.$this->format);
	}
	
	/**
	 * Get a specific roll-call vote, including a complete list of member positions.
	 *
	 * @param string $congressnumber
	 * @param string $chamber
	 * @param int $sessionnumber
	 * @param int $rollcallnumber
	 */
	public function getRollCallVotes($congressnumber,$chamber,$sessionnumber,$rollcallnumber)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/sessions/'.$sessionnumber.'/votes/'.$rollcallnumber.'.'.$this->format);
	}
	
	/**
	 * Get vote information in four categories: missed votes, party votes, lone no votes and perfect votes.
	 *
	 * @param string $congressnumber
	 * @param string $chamber
	 * @param string $votetype
	 */
	public function getVotesByType($congressnumber,$chamber,$votetype)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/votes/'.$votetype.'.'.$this->format);
	}
	
	/**
	 * Get all votes in a particular month.
	 *
	 * @param string $chamber
	 * @param int $year
	 * @param int $month
	 */
	public function getVotesByMonth($chamber,$year,$month)
	{
		return $this->_serviceCall($this->uri.'/'.$chamber.'/votes/'.$year.'/'.$month.'.'.$this->format);
	}
	
	/**
	 * Get all votes in a particular date range (fewer than 30 days).
	 *
	 * @param string $chamber
	 * @param string $startdate
	 * @param string $enddate
	 */
	public function getVotesByDateRange($chamber,$startdate,$enddate)
	{
		return $this->_serviceCall($this->uri.'/'.$chamber.'/votes/'.$startdate.'/'.$enddate.'.'.$this->format);
	}
	
	/**
	 * Get Senate votes on presidential nominations.
	 * Nomination data includes roll-call votes only; nominations approved by unanimous consent or voice vote are not returned.
	 *
	 * @param int $congressnumber
	 */
	public function getNominationVotes($congressnumber)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/nominations.'.$this->format);
	}
	
	/**
	 * Get summaries of the 20 most recent bills by type.
	 * For the current Congress, "recent bills" can be one of four types.
	 * For previous Congresses, "recent bills" means the last 20 bills of that Congress.
	 *
	 * @param int $congressnumber
	 * @param string $chamber
	 * @param string $type
	 */
	public function getRecentBills($congressnumber,$chamber,$type)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/bills/'.$type.'.'.$this->format);
	}
	
	/**
	 * Get the 20 bills most recently introduced or updated by a particular member.
	 * Results can include more than one Congress.
	 *
	 * @param int $memberid
	 * @param string $type
	 */
	public function getBillsByMember($memberid,$type)
	{
		return $this->_serviceCall($this->uri.'/members/'.$memberid.'/bills/'.$type.'.'.$this->format);
	}
	
	/**
	 * Get additional details about a particular bill, including actions taken.
	 *
	 * @param int $congressnumber
	 * @param string $billid
	 */
	public function getBillDetails($congressnumber,$billid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/bills/'.$billid.'.'.$this->format);
	}
	
	/**
	 * Get bill subjects.
	 *
	 * @param int $congressnumber
	 * @param string $billid
	 */
	public function getBillSubjects($congressnumber,$billid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/bills/'.$billid.'/subjects.'.$this->format);
	}
	
	/**
	 * Get bill amendments.
	 *
	 * @param int $congressnumber
	 * @param string $billid
	 */
	public function getBillAmendments($congressnumber,$billid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/bills/'.$billid.'/amendments.'.$this->format);
	}
	
	/**
	 * Get related bills.
	 *
	 * @param int $congressnumber
	 * @param string $billid
	 */
	public function getRelatedBills($congressnumber,$billid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/bills/'.$billid.'/related.'.$this->format);
	}
	
	/**
	 * Get information about the cosponsors of a particular bill.
	 *
	 * @param int $congressnumber
	 * @param string int $billid
	 */
	public function getBillCosponsors($congressnumber,$billid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/bills/'.$billid.'/cosponsors.'.$this->format);
	}
	
	/**
	 * Get lists of presidential nominations for civilian positions.
	 *
	 * @param int $congressnumber
	 * @param string $nominationcat
	 */
	public function getNomineeLists($congressnumber,$nominationcat)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/nominees/'.$nominationcat.'.'.$this->format);
	}
	
	/**
	 * Get details about a particular presidential civilian nomination.
	 *
	 * @param int $congressnumber
	 * @param int $nomineeid
	 */
	public function getNomineeDetails($congressnumber,$nomineeid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/nominees/'.$nomineeid.'.'.$this->format);
	}
	
	/**
	 * Get the 20 most recent nominees from a particular state.
	 *
	 * @param int $congressnumber
	 * @param string $state
	 */
	public function getNomineesByState($congressnumber,$state)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/nominees/state/'.$state.'.'.$this->format);
	}
	
	/**
	 * Get party membership counts for all states (current Congress only).
	 */
	public function getStatePartyCounts()
	{
		return $this->_serviceCall($this->uri.'/states/members/party.'.$this->format);
	}
	
	/**
	 * Get a list of Senate or House committees.
	 *
	 * @param int $congressnumber
	 * @param string $chamber
	 */
	public function getCommittees($congressnumber,$chamber)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/committees.'.$this->format);
	}
	
	/**
	 * Get the members of a particular committee.
	 *
	 * @param int $congressnumber
	 * @param string $chamber
	 * @param int $committeeid
	 */
	public function getCommitteeMembers($congressnumber,$chamber,$committeeid)
	{
		return $this->_serviceCall($this->uri.'/'.$congressnumber.'/'.$chamber.'/committees/'.$committeeid.'.'.$this->format);
	}
	
	/**
	 * Get today's schedule for the House or Senate.
	 * The response also includes a list of bills to be considered, if available.
	 * This request is available for the current Congress only.
	 *
	 * @param string $chamber
	 */
	public function getChamberSchedule($chamber)
	{
		return $this->_serviceCall($this->uri.'/'.$chamber.'/schedule.'.$this->format);
	}
	
	/**
	 * Prepares and returns the api uri
	 */
	protected function _prepareURI($uri,$params=null)
	{
		$querystring = '';
		if (is_array($params) && count($params)>0)
		{
			$querystring = '&'.http_build_query($params);
		}
		
		return $uri.'?api-key='.$this->apikey.$querystring;
	}
	
	/**
	 * Makes the web service call to the api.
	 *
	 * @param string $uri
	 * @param array $params
	 */
	protected function _serviceCall($uri,$params=null)
	{
		$results = '';
		
		$uri = $this->_prepareURI($uri,$params);
		
		if (extension_loaded('curl'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $uri);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			$results = curl_exec($ch);
			curl_close($ch);
		}
		else
		{
			$results = file_get_contents($uri);
		}
		
		return $results;
	}
	
}
?>