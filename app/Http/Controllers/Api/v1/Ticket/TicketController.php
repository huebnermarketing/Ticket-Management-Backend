<?php

namespace App\Http\Controllers\Api\v1\Ticket;

use App\Http\Controllers\Controller;
use App\Mail\SendTicketCreateEmailNotification;
use App\Models\AppointmentTypes;
use App\Models\CustomerLocations;
use App\Models\CustomerPhones;
use App\Models\Customers;
use App\Models\PaymentTypes;
use App\Models\ProblemType;
use App\Models\TicketComments;
use App\Models\Tickets;
use App\Models\TicketStatus;
use App\Models\User;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\Ticket\TicketRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RestResponse;
use Validator;

class TicketController extends Controller
{
    private $customerRepository;
    private $ticketRepository;
    private $userRepository;
    public function __construct(CustomerRepositoryInterface $customerRepository,
                                TicketRepositoryInterface $ticketRepository,UserRepositoryInterface $userRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->ticketRepository = $ticketRepository;
        $this->userRepository = $userRepository;
    }

    private function generateTicketUrlSlug()
    {
        $digit = 5;
        $slugNumber = substr(str_shuffle("0123456789"), 0, $digit);
        $checkSlugNumber = Tickets::where('url_slug', $slugNumber)->count();
        if ($checkSlugNumber > 0) {
            $this->generateTicketUrlSlug();
        }
        return $slugNumber;
    }

    public function index(Request $request)
    {
        try{
            $filters = [
                'total_record' => $request->total_record,
                'order_by' => $request->order_by,
                'sort_value' => $request->sort_value
            ];
            $getAllTickets = $this->ticketRepository->getTickets($filters);
            if(empty($getAllTickets)){
                return RestResponse::warning('Ticket not found.');
            }
            $tickets['allTicket'] = $getAllTickets;
            $tickets['ticketDashboard'] = $this->ticketRepository->ticketListDashboard();
            return RestResponse::Success($tickets, 'Ticket retrieve successfully.');
        } catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = Validator::make($request->all(), [
                'ticket_type' => 'required',
                'is_existing_customer' => 'required',
                'customer_name' => 'required',
                'address_line1' => 'required',
                'area' => 'required',
                'zipcode' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'primary_mobile' => 'required',

                'problem_type_id' => 'required',
                'problem_title' => 'required',
                'due_date' => 'required',
                'ticket_status_id' => 'required',
                'priority_id' => 'required',
                'assigned_user_id' => 'required',
                'appointment_type_id' => 'required',

                'ticket_amount' => 'required|numeric|gt:0',
                'payment_type_id' => 'required',
                'collected_amount' => 'required|numeric|gte:0',
                'remaining_amount' => 'required',
                'payment_mode' => 'required'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $splitCustomerName = explode(' ', $request['customer_name'], 2);
            $ticketUrlSlug = $this->generateTicketUrlSlug();
            $request->merge(['first_name' => $splitCustomerName[0],
                'last_name' => !empty($splitCustomerName[1]) ? $splitCustomerName[1] : '',
                'url_slug' => $ticketUrlSlug
            ]);

            if($request['is_existing_customer'] == 1){
                $customerPayload = $request->only(['first_name','last_name','email']);
                $updateCustomer = $this->customerRepository->updateCustomer($customerPayload,$request['customer_id']);

                CustomerPhones::where(['customer_id' => $request['customer_id'],'is_primary' => 1])
                    ->update(['phone'=>$request['primary_mobile']]);

                $customerAddressPayload = $request->only(['address_line1','company_name','area','city','zipcode','state','country']);
                $updateAddress = $this->customerRepository->updateAddress($customerAddressPayload,$request['customer_locations_id']);
            } else {
                $customerPayload = $request->only(['first_name','last_name','email','primary_mobile']);
                $customerPayload['addresses'][] = [
                    'address_line1' => $request['address_line1'],
                    'company_name' => $request['company_name'],
                    'area' => $request['area'],
                    'city' => $request['city'],
                    'state' => $request['state'],
                    'zipcode' => $request['zipcode'],
                    'country' => $request['country'],
                    'is_primary' => 1,
                ];
                $createCustomer = $this->customerRepository->storeCustomer($customerPayload);
                if(!$createCustomer){
                    return RestResponse::warning('Customer create failed.');
                }
                $getCustomerLocation = CustomerLocations::where(['customer_id' => $createCustomer['id'],'is_primary' => 1])->first();
                $request->merge(['customer_id' => $createCustomer['id'],'customer_locations_id' => $getCustomerLocation['id']]);
            }

            $createTicket = $this->ticketRepository->storeTicket($request);
            if(!$createTicket){
                return RestResponse::warning('Ticket create failed.');
            }

            $findUser = $this->userRepository->findUser($request['assigned_user_id']);
            $authUser = Auth::user();
            $mailData = [
                'assign_user_name' => $findUser['first_name'] .' '.$findUser['last_name'],
                'ticket_id' => $ticketUrlSlug,
                'ticket_title' => $request['problem_title'],
                'due_date' => $request['due_date'],
                'description' => !empty($request['description']) ? $request['description'] : null,
                'assigned_by' => $authUser['first_name'] .' '. $authUser['last_name'],
                'ticket_detail_url' => 'd'
            ];
            Mail::to($findUser['email'])->send(new SendTicketCreateEmailNotification($mailData));

            DB::commit();
            return RestResponse::Success([],'Ticket created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function show()
    {
        try{
            $data['customers'] = Customers::join('customer_phones', 'customer_phones.customer_id', 'customers.id')
                ->where('customer_phones.is_primary',1)->select('customers.*','customer_phones.customer_id','customer_phones.phone')->get();

            /*$data['assign_engineer'] = User::with(['role'])->whereHas('role', function($qry){
                $qry->where('role_slug','user');
            })->get();*/
            $data['assign_engineer'] = User::all();
            $data['problem_types'] = ProblemType::all();
            $data['ticket_status'] = TicketStatus::all();
            $data['appointment_type'] = AppointmentTypes::all();
            $data['payment_status'] = PaymentTypes::all();
            $data['payment_mode'] = config('constant.PAYMENT_MODE');
            return RestResponse::Success($data, 'Ticket details retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function view($ticketId)
    {
        try{
            $getTicket = Tickets::with('comments')->where('id',$ticketId)->ticketRelations()->first();
            if(empty($getTicket)){
                return RestResponse::warning('Ticket not found.');
            }
            $ticketData = [
              'ticket_detail' => $getTicket,
              'ticket_status' => TicketStatus::all(),
            ];
            return RestResponse::Success($ticketData, 'Ticket details retrieve successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $validate = Validator::make($request->all(), [
                'ticket_type' => 'required',
                //'is_existing_customer' => 'required',
                'customer_name' => 'required',
                'address_line1' => 'required',
                'area' => 'required',
                'zipcode' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'primary_mobile' => 'required',

                'problem_type_id' => 'required',
                'problem_title' => 'required',
                'due_date' => 'required',
                'ticket_status_id' => 'required',
                'priority_id' => 'required',
                'assigned_user_id' => 'required',
                'appointment_type_id' => 'required',

                'ticket_amount' => 'required|numeric|gt:0',
                'payment_type_id' => 'required',
                'collected_amount' => 'required|numeric|gte:0',
                'remaining_amount' => 'required',
                'payment_mode' => 'required'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function addComment(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'comment' => 'required'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $authUser = Auth::user();
            $createData = [
              'user_id' => $authUser['id'],
              'ticket_id' => $request['ticket_id'],
              'comment' => $request['comment'],
              'comment_date' => Carbon::now()->toDateString(),
            ];
            $addComment = TicketComments::create($createData);
            if(!$addComment){
                return RestResponse::warning('Ticket comment add failed.');
            }
            return RestResponse::Success([],'Ticket comment added successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'ticket_status_id' => 'required'
            ]);
            if ($validate->fails()) {
                return RestResponse::validationError($validate->errors());
            }
            $changeStatus = Tickets::where('id',$request['ticket_id'])->update([
                'ticket_status_id' => $request['ticket_status_id']
            ]);
            if(!$changeStatus){
                return RestResponse::warning('Ticket status update failed.');
            }
            return RestResponse::Success([],'Ticket status updated successfully.');
        }catch (\Exception $e) {
            return RestResponse::error($e->getMessage(), $e);
        }
    }
}
