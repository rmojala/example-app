<script setup>
import { Head, Link, useHttp } from "@inertiajs/vue3";
import { bulkUpdate } from '@/actions/App/Http/Controllers/Admin/UserController';
import { toast } from 'vue-sonner';
import { AlertCircle } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
});

const http = useHttp({
  users: props.users.data.map((user) => ({ ...user })),
});

function showErrorToast() {
  toast.error('Failed to save changes');
}

function save() {
  const { method, url } = bulkUpdate();
  http.submit(method, url, {
    onError: showErrorToast,
    onHttpException: showErrorToast,
    onNetworkError: showErrorToast,
  });
}
</script>

<template> 
  <Head title="Users" /> 

  <div class="space-y-8">
    <h1 class="text-2xl font-semibold">Users</h1>

    <div class="flex items-center gap-4">
      <Button
        @click="save"
        :disabled="!http.isDirty || http.processing"
      >
        {{ http.processing ? 'Saving...' : 'Save' }}
      </Button>
      <span
        v-if="http.recentlySuccessful"
        class="text-sm italic"
      >
        Saved
      </span>
      <span
        v-else-if="!http.isDirty"
        class="italic text-sm"
      >
        No unsaved changes
      </span>
    </div>

    <Alert>
      <AlertCircle />
      <AlertTitle>User Management</AlertTitle>
      <AlertDescription>
        Administrators can manage users' ability to create notes.
        Try toggling the checkboxes and saving the changes.
      </AlertDescription>
    </Alert>
   
    <Table v-if="http.users.length > 0">
      <TableHeader>
        <TableRow class="hover:bg-transparent">
          <TableHead>ID</TableHead>
          <TableHead>Email</TableHead>
          <TableHead>Name</TableHead>
          <TableHead>Can Create Notes</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="user in http.users">
          <TableCell>{{ user.id }}</TableCell>
          <TableCell>{{ user.email }}</TableCell>
          <TableCell>{{ user.name }}</TableCell>
          <TableCell>
            <Checkbox v-model="user.canCreateNotes" />
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

    <p v-else class="italic">There are no non-admin users.</p>
  </div>
</template> 
