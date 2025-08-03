'use client';

import { useState, useEffect, useRef } from 'react';

interface ApiResponse {
  status?: string;
  output?: string;
  branches?: string[];
  error?: string;
  local?: string;
  remote?: string;
}

export default function Dashboard() {
  const [branches, setBranches] = useState<string[]>([]);
  const [message, setMessage] = useState<string>('');
  const [newBranch, setNewBranch] = useState<string>('');
  const [switchBranch, setSwitchBranch] = useState<string>('');
  const selectRef = useRef<HTMLSelectElement>(null);

  const fetchBranches = async (): Promise<void> => {
    try {
      const res = await fetch('/api/git/branch-list');
      const data: ApiResponse = await res.json();
      setBranches(data.branches || []);
    } catch (error) {
      console.error('Error fetching branches:', error);
      setMessage('Error fetching branches');
    }
  };

  const handlePull = async (): Promise<void> => {
    try {
      const res = await fetch('/api/git/pull');
      const data: ApiResponse = await res.json();
      setMessage(data.output || 'Pull completed');
    } catch (error) {
      console.error('Error pulling:', error);
      setMessage('Error pulling latest code');
    }
  };

  const handlePush = async (): Promise<void> => {
    try {
      const res = await fetch('/api/git/push');
      const data: ApiResponse = await res.json();
      setMessage(data.output || 'Push completed');
    } catch (error) {
      console.error('Error pushing:', error);
      setMessage('Error pushing changes');
    }
  };

  const handleCreateBranch = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
    e.preventDefault();
    if (!newBranch.trim()) return;

    try {
      const res = await fetch(`/api/git/create-branch?name=${encodeURIComponent(newBranch.trim())}`);
      const data: ApiResponse = await res.json();
      setMessage(data.output || 'Branch created successfully');
      setNewBranch('');
      fetchBranches();
    } catch (error) {
      console.error('Error creating branch:', error);
      setMessage('Error creating branch');
    }
  };

  const handleSwitchBranch = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
    e.preventDefault();
    if (!switchBranch.trim()) return;

    try {
      const res = await fetch(`/api/git/switch-branch?name=${encodeURIComponent(switchBranch.trim())}`);
      const data: ApiResponse = await res.json();
      setMessage(data.output || 'Branch switched successfully');
      setSwitchBranch('');
      fetchBranches();
    } catch (error) {
      console.error('Error switching branch:', error);
      setMessage('Error switching branch');
    }
  };

  const handleDeleteBranch = async (branch: string): Promise<void> => {
    if (!branch || !confirm(`Are you sure you want to delete branch: ${branch}?`)) {
      return;
    }

    try {
      const res = await fetch(`/api/git/delete-branch?name=${encodeURIComponent(branch)}`);
      const data: ApiResponse = await res.json();
      setMessage(`Local: ${data.local || 'N/A'}\nRemote: ${data.remote || 'N/A'}`);
      fetchBranches();
    } catch (error) {
      console.error('Error deleting branch:', error);
      setMessage('Error deleting branch');
    }
  };

  const handleDeleteSelectedBranch = (): void => {
    if (selectRef.current?.value) {
      handleDeleteBranch(selectRef.current.value);
    }
  };

  useEffect(() => {
    fetchBranches();
  }, []);

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold mb-6 text-center">GitHub Deploy Dashboard</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <button
          onClick={handlePull}
          className="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg"
        >
          Pull Latest Code
        </button>

        <button
          onClick={handlePush}
          className="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg"
        >
          Push Changes
        </button>

        <form onSubmit={handleCreateBranch} className="col-span-1 md:col-span-2 flex gap-4">
          <input
            type="text"
            placeholder="New Branch Name"
            value={newBranch}
            onChange={(e) => setNewBranch(e.target.value)}
            className="flex-1 border rounded-lg px-4 py-2"
            required
          />
          <button
            type="submit"
            className="bg-purple-500 hover:bg-purple-600 text-white font-semibold px-6 py-2 rounded-lg"
          >
            Create Branch
          </button>
        </form>

        <form onSubmit={handleSwitchBranch} className="col-span-1 md:col-span-2 flex gap-4 ">
          <input
            type="text"
            placeholder="Switch to Branch"
            value={switchBranch}
            onChange={(e) => setSwitchBranch(e.target.value)}
            className="flex-1 border rounded-lg px-4 py-2"
            required
          />
          <button
            type="submit"
            className="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-lg"
          >
            Switch Branch
          </button>
        </form>

        <div className="col-span-1 md:col-span-2 flex items-center gap-4 mt-4">
          <select
            ref={selectRef}
            className="flex-1 border rounded-lg px-4 py-2"
          >
            {branches.map((branch: string) => (
              <option key={branch} value={branch}>{branch}</option>
            ))}
          </select>
          <button
            onClick={handleDeleteSelectedBranch}
            className="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2 rounded-lg"
          >
            Delete Branch
          </button>
        </div>
      </div>

      {message && (
        <pre className="mt-8 bg-gray-900 text-green-400 text-sm p-4 rounded-lg h-64 overflow-y-auto whitespace-pre-wrap">
          {message}
        </pre>
      )}
    </div>
  );
}
