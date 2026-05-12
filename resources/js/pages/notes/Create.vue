<script setup>
import { Form, Head } from "@inertiajs/vue3";
import { index, store } from '@/actions/App/Http/Controllers/NoteController';
import { AlertCircle } from 'lucide-vue-next';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import SharedWithHelp from './components/SharedWithHelp.vue';

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
});

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Notes',
        href: index(),
      },
      {
        title: 'Create Note',
      },
    ],
  },
});
</script>

<template>
  <Head title="Create Note" />
  
  <h1 class="text-2xl font-semibold mb-8">Create Note</h1>
  
  <Form
    :action="store()"
    #default="{ errors, processing }"
    class="max-w-lg space-y-4"
  >
    <div class="space-y-3">
      <Label for="title">Title</Label>
      <Input id="title" name="title" />
      <InputError :message="errors.title" />
    </div>

    <div class="space-y-3">
      <Label for="details">Details</Label>
      <Textarea
        id="details"
        name="details"
        placeholder="Type note details here."
        class="h-64"
      />
      <InputError :message="errors.details" />
    </div>

    <div class="space-y-3">
      <div class="flex items-center gap-2">
        <Label>Shared With</Label>
        <SharedWithHelp />
      </div>

      <Alert>
        <AlertCircle />
        <AlertTitle>Info</AlertTitle>
        <AlertDescription>
          To keep the app simple, a user is allowed to choose from a list
          of all users who to share the note with. I wouldn't do this in
          a real app.
        </AlertDescription>
      </Alert>
   
      <div
        v-for="user in users.data"
        :key="user.id"
        class="flex items-center gap-3"
      >
        <Checkbox
          :id="user.email"
          name="sharedWith[]"
          :value="user.id"
        />
        <Label :for="user.email">{{ user.email }}</Label>
      </div>
    </div>

    <Button type="submit" :disabled="processing" class="my-4">
      {{ processing ? 'Saving...' : 'Save' }}
    </Button>
  </Form>
</template>
