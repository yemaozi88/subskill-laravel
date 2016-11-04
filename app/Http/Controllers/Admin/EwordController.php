<?php

namespace App\Http\Controllers\Admin;

use App\EwordResult;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\Helper;
use App\EwordSession;

use DB;

class EwordController extends Controller
{
    public function index(Request $request) {
        $college_info = Helper::get_college_info();
        return view('admin/eword/index', [
            'college_info' => $college_info,
        ]);
    }

    public function list_by_date(Request $request) {
        $test_type = $request->input('test_type');
        $group_name = $request->input('group_name');

        // TODO(sonicmisora): Add group constraint
        $dates = EwordSession::by_date($test_type == 'with_wav', true);
        return view('admin/eword/list_by_date', [
            'test_type' => $test_type,
            'group_name' => $group_name,
            'dates' => $dates,
        ]);
    }

    public function detail(Request $request) {
        $test_type = $request->input('test_type');
        $group_name = $request->input('group_name');
        $date = $request->input('date');

        // TODO(sonicmisora): Add group constraint
        $results = EwordResult::select('session_id', 'is_correct', 'elapsed_time')
            ->whereIn('session_id', function ($query) use ($date, $test_type) {
                $query->selectRaw('id')
                    ->from('eword_sessions')
                    ->whereRaw('DATE(created_at) = ?', [$date])
                    ->where('with_wav', $test_type == 'with_wav')
                    ->where('is_test', true);
            })
            ->with(['session' => function($query) {
                $query->select('id', 'user_id');
            }, 'session.user' => function($query) {
                $query->select('id', 'username');
            }])
            ->get();
        $overall_stat = $this->compute_stat($results);

        return view('admin/eword/detail', [
            'date' => $date,
            'results' => $results,
            'overall_stat' => $overall_stat,
        ]);
    }

    // Private functions
    private function compute_stat($results) {

    }
}
