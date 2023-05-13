<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($user_id = $request->get('user_id')) {
            return response($this->repository->getUsersJobs($user_id));
        }

        if ($request->has('__authenticatedUser')
            && in_array($request->__authenticatedUser->user_type, [
                config('constants.admin_role_id'),
                config('constants.superadmin_role_id')
            ])
        ) {
            return response($this->repository->getAll($request));
        }

        return response([]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        // This can be returned directly which save memory, but due to code readability we use variables.
        // We can avoid variable where possible.
        $job = $this
            ->repository
            ->with('translatorJobRel.user')
            ->find($id);

        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $response = $this
            ->repository
            ->store($request->get('__authenticatedUser'), $request->all());

        return response($response);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $response = $this
            ->repository
            ->updateJob(
                $id,
                $request->except(['_token', 'submit']),
                $request->get('__authenticatedUser')
            );

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $response = $this->repository->storeJobEmail($request->all());

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        // using ternary operator seem reasonable here because it looks cleaner.
        // and return values are null or response.
        return $request->has('user_id')
            ? response($this->repository->getUsersJobsHistory($request->get('user_id'), $request))
            : null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        // I prefer chaining in new line due to readability
        $response = $this
            ->repository
            ->acceptJob($request->all(), $request->get('__authenticatedUser'));

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $response = $this
            ->repository
            ->acceptJobWithId($request->get('job_id'), $request->get('__authenticatedUser'));

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $response = $this
            ->repository
            ->cancelJobAjax($request->all(), $request->get('__authenticatedUser'));

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $response = $this->repository->endJob($request->all());

        return response($response);
    }

    public function customerNotCall(Request $request)
    {
        $response = $this->repository->customerNotCall($request->all());

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $response = $this->repository->getPotentialJobs($request->get('__authenticatedUser'));

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        if ($request->get('flagged') === 'true' && $request->get('admincomment') === '') {
            return 'Please, add comment';
        }

        $distance = $request->get('distance', '');
        $time = $request->get('time', '');
        $jobid = $request->get('jobid'); // previously no default value is present which can result in failure.
        $session = $request->get('session_time', '');
        $flagged = $request->get('flagged') === 'true' ? 'yes' : 'no';
        $manually_handled = $request->get('manually_handled') === 'true' ? 'yes' : 'no';
        $by_admin = $request->get('by_admin') === 'true' ? 'yes' : 'no';
        $admincomment = $request->get('admincomment', '');

        if ($time || $distance) {
            Distance::where('job_id', $jobid)
                ->update([
                    'distance' => $distance,
                    'time' => $time
                ]);
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', $jobid)
                ->update([
                    'admin_comments' => $admincomment,
                    'flagged' => $flagged,
                    'session_time' => $session,
                    'manually_handled' => $manually_handled,
                    'by_admin' => $by_admin
                ]);
        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $response = $this->repository->reopen($request->all());

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $job = $this->repository->find($request->get('jobid'));
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $job = $this->repository->find($request->get('jobid'));
            $this->repository->sendSMSNotificationToTranslator($job);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }

        return response(['success' => 'SMS sent']);
    }

}
