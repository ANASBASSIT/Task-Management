<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\task;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TaskController extends Controller
{
   
   public function addTask(Request $request)
   {
       $request->validate([
           'description' => 'required|string|max:255',
           'user_id' => 'required|integer|exists:users,id', 
       ]);

       try {
           $task = Task::create([
               'description' => $request->description,
               'user_id' => $request->user_id, 
           ]);

           return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
       } catch (Exception $e) {
           Log::error('Error creating task: ' . $e->getMessage());
           return response()->json(['error' => 'Error creating task'], 500);
       }
   }

   

   public function editTask(Request $request)
   {
      
       $request->validate([
           'description' => 'sometimes|required|string|max:255',
           'completed' => 'sometimes|boolean',
           'id' => 'required|integer|exists:tasks,id', 
       ]);
   
       try {
           // Find the task by ID from the request body
           $task = Task::findOrFail($request->id); 
   
           // Check if admin
           if (auth('admins')->check()) {
               
               $task->update([
                   'description' => $request->description ?? $task->description,
                   'completed' => $request->completed ?? $task->completed,
               ]);
           } else {
               // Ensure the user is the owner of the task
               if ($task->user_id !== auth('api')->id()) { 
                   Log::error('Unauthorized access attempt by user ID: ' . auth()->id());
                   return response()->json(['error' => 'Unauthorized'], 403);
               }
            // Check if the user is trying to update the description
            if ($request->has('description')) {
                return response()->json([
                    'error' => 'You cannot update the description field as a regular user.'
                ], 400);
            }
               // Allow updating only the completed field
               if ($request->has('completed')) {
                   $task->update([
                       'completed' => $request->completed,
                   ]);
               } else {
                   return response()->json(['error' => 'Only the completed field can be updated for regular users'], 400);
               }
           }
   
           Log::info('Task updated successfully', ['task_id' => $task->id]);
   
           return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
       } catch (ModelNotFoundException $e) {
           Log::error('Task not found', ['task_id' => $request->id]);
           return response()->json(['error' => 'Task not found'], 404);
       } catch (Exception $e) {
           Log::error('Error updating task: ' . $e->getMessage());
           return response()->json(['error' => 'Error updating task'], 500);
       }
   }
   
   
   
   
   

   // Admin: Delete a task
   public function deleteTask(Request $request)
   {
       $request->validate([
           'id' => 'required|integer|exists:tasks,id', 
       ]);
   
       try {
           $task = Task::find($request->id);
           
           if (!auth('admins')->id()) {
               return response()->json(['error' => 'Unauthorized'], 403);
           }
   
          
   
           $task->delete();
   
           return response()->json(['message' => 'Task deleted successfully']);
       } catch (Exception $e) {
           Log::error('Error deleting task: ' . $e->getMessage());
           return response()->json(['error' => 'Error deleting task'], 500);
       }
   }
   public function getTasks(Request $request)
   {
       // Define the number of items per page
       $perPage = $request->input('per_page', 2); // Default to 2 if not provided
   
       // Get the authenticated user ID
       $userId = auth('api')->id();
       Log::info('Authenticated user ID: ' . $userId);
   
       // Retrieve tasks with pagination
       $tasks = Task::where('user_id', $userId) 
                     ->paginate($perPage);
   
       if ($tasks->isEmpty()) {
           Log::info('No tasks found for user ID: ' . $userId);
       }
   
       return response()->json($tasks);
   }
   

   
}
