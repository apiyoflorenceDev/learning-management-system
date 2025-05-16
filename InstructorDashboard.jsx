import { useState } from "react";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";
import { Home, BookOpen, Users, Settings } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

const courseData = [
  { name: "Course 1", students: 45 },
  { name: "Course 2", students: 30 },
  { name: "Course 3", students: 50 },
];

export default function InstructorDashboard() {
  const [activeTab, setActiveTab] = useState("dashboard");

  return (
    <div className="flex min-h-screen bg-gray-100">
      {/* Sidebar */}
      <div className="w-64 bg-white p-5 shadow-lg">
        <h2 className="text-xl font-bold mb-5">Instructor Panel</h2>
        <nav>
          <Button onClick={() => setActiveTab("dashboard")} className="w-full mb-2 flex gap-2">
            <Home size={18} /> Dashboard
          </Button>
          <Button onClick={() => setActiveTab("courses")} className="w-full mb-2 flex gap-2">
            <BookOpen size={18} /> Courses
          </Button>
          <Button onClick={() => setActiveTab("students")} className="w-full mb-2 flex gap-2">
            <Users size={18} /> Students
          </Button>
          <Button onClick={() => setActiveTab("settings")} className="w-full mb-2 flex gap-2">
            <Settings size={18} /> Settings
          </Button>
        </nav>
      </div>

      {/* Main Content */}
      <div className="flex-1 p-6">
        {activeTab === "dashboard" && (
          <div>
            <h1 className="text-2xl font-bold mb-4">Dashboard</h1>
            <div className="grid grid-cols-3 gap-4">
              <Card>
                <CardContent className="p-4">
                  <h3 className="text-lg font-semibold">Total Courses</h3>
                  <p className="text-2xl">3</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4">
                  <h3 className="text-lg font-semibold">Total Students</h3>
                  <p className="text-2xl">125</p>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="p-4">
                  <h3 className="text-lg font-semibold">Pending Grading</h3>
                  <p className="text-2xl">8</p>
                </CardContent>
              </Card>
            </div>
            <div className="mt-6 bg-white p-4 shadow rounded-lg">
              <h3 className="text-lg font-semibold mb-2">Student Enrollment</h3>
              <ResponsiveContainer width="100%" height={300}>
                <BarChart data={courseData}>
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="students" fill="#4F46E5" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
