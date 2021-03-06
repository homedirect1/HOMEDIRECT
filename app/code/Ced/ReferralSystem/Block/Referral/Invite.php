<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_ReferralSystem
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\ReferralSystem\Block\Referral;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Invite extends \Magento\Framework\View\Element\Template {

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

	public function __construct(
		Context $context,
		\Magento\Customer\Model\Session $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        DataObjectHelper $dataObjectHelper,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\RequestInterface $httpRequest
    ) {
        $this->customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        $this->_customerMapper = $customerMapper;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->request = $httpRequest;
		parent::__construct ( $context );
	}

	protected function _prepareLayout()
    {
		parent::_prepareLayout ();
		$this->pageConfig->getTitle ()->set ( "Refer A Friend" );
		return $this;
	}

	public function generateReferralLink()
    {
		$invitation_code = $this->getInvitationCode();
		$code = $this->getUrl("customer/account/create", ["affid"=>$invitation_code]);
		return $code;
	}

	public function createInvitationCode($customerId)
    {
        $customerData = [ "invitation_code" => $this->generateInvitationCode()];
        $savedCustomerData = $this->_customerRepository->getById($customerId);
        $customerm = $this->_customerDataFactory->create();

        $customerData = array_merge($this->_customerMapper->toFlatArray($savedCustomerData), $customerData);
        $customerData['id'] = $customerId;
        $this->_dataObjectHelper->populateWithArray(
            $customerm,
            $customerData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        try{
            $this->_customerRepository->save($customerm);
        }catch(\Exception $e){
            return false;
        }
    }

	public function generateInvitationCode() 
    {
        $length = 6;
        $rndId = hash('md5', uniqid(rand(),1));
        $rndId = strip_tags($rndId); 
        $rndId = str_replace([".", "$"],"",$rndId);
        $rndId = strrev(str_replace("/","",$rndId));
        if ($rndId !== null){
            return strtoupper(substr($rndId, 0, $length));
        } 
        return strtoupper($rndId);
    }

    public function generateFBReferralLink()
    {
		$code = $this->getUrl("customer/account/create", ["affid"=>'facebook-'.$this->getInvitationCode()]);
		return $code;
	}

	public function generateGoogleReferralLink()
    {
		$code = $this->getUrl("customer/account/create", ["affid"=>'google-'.$this->getInvitationCode()]);
		return $code;
	}

	public function generateTwitterReferralLink()
    {
		$code = $this->getUrl("customer/account/create", ["affid"=>'twitter-'.$this->getInvitationCode()]);
		return $code;
	}

	public function generateWatsappReferralLink(){
		$code = $this->getUrl("customer/account/create", ["affid"=>'whatsapp-'.$this->getInvitationCode()]);
		return $code;
	}

    public function getInvitationCode()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customerModel = $this->_customerRepository->getById($customerId);
        $invitationCode = $customerModel->getCustomAttribute('invitation_code');
        if($invitationCode){
            $invitationCode = $invitationCode->getValue();
        }else{
            $this->createInvitationCode($customerId);
            $this->getInvitationCode();
        }
        return $invitationCode;
    }

    public function getDefaultMessage(){
	    return $this->_scopeConfig->getValue('referral/system/default_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDefaultSubject(){
        return $this->_scopeConfig->getValue('referral/system/default_email_subject', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function checkDevice(){
        $useragent = $this->request->getServer('HTTP_USER_AGENT');
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
            return true;
        }else{
            return false;
        }
    }
}
